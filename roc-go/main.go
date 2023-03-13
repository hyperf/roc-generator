package main

import (
	"flag"
	"fmt"
	"github.com/hyperf/roc-generator/roc-go/cmd"
	"github.com/hyperf/roc-generator/roc-go/generator"
	"google.golang.org/protobuf/compiler/protogen"
	"google.golang.org/protobuf/proto"
	"google.golang.org/protobuf/types/pluginpb"
	"os"
)

func main() {
	if len(os.Args) > 1 {
		cmd.Execute()
		os.Exit(0)
	}

	input := generator.ReadStdin()
	if len(input) == 0 {
		fmt.Println("The root command must run by protoc.")
		os.Exit(0)
	}

	var req pluginpb.CodeGeneratorRequest

	_ = proto.Unmarshal(input, &req)
	opts := protogen.Options{
		ParamFunc: flag.CommandLine.Set,
	}
	plugin, err := opts.New(&req)
	if err != nil {
		panic(err)
	}

	for _, file := range plugin.Files {
		if !file.Generate {
			continue
		}

		fmt.Fprintf(os.Stderr, "path:%s\n", file.GoImportPath)
		genF := plugin.NewGeneratedFile(fmt.Sprintf("%s.pb.go", file.GeneratedFilenamePrefix), file.GoImportPath)
		genF.Write([]byte("package xxx"))
	}

	resp := plugin.Response()
	out, err := proto.Marshal(resp)
	if err != nil {
		fmt.Printf("%s", err)
		panic(err)
	}

	// 相应输出到stdout, 它将被 protoc 接收
	fmt.Fprintf(os.Stdout, string(out))
}
