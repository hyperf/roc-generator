package main

import (
	"fmt"
	"github.com/hyperf/roc-generator/roc-go/cmd"
	"github.com/hyperf/roc-generator/roc-go/generator"
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
	fmt.Println(req)
}
