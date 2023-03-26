package cmd

import (
	"fmt"
	"github.com/spf13/cobra"
	"os"
	"os/exec"
	"path/filepath"
)

var genCmd = &cobra.Command{
	Use:   "gen:roc [protobuf]",
	Short: "Generate protoc command",
	Long:  `Generate protoc command which used to generate structs and interfaces by protobuf.`,
	// Uncomment the following line if your bare application
	// has an action associated with it:
	Run: func(cmd *cobra.Command, args []string) {
		bin, err := os.Executable()
		if err != nil {
			panic(err)
		}

		if len(args) == 0 {
			fmt.Println("The protobuf must be required.")
			os.Exit(1)
		}

		protobuf := args[0]
		path := cmd.Flag("path").Value
		output := cmd.Flag("output").Value
		if path.String() == "" {
			path.Set(filepath.Dir(protobuf))
		}
		if output.String() == "" {
			wd, err := os.Getwd()
			if err != nil {
				panic(err)
			}
			output.Set(wd)
		}

		executor := exec.Command(
			"protoc",
			"--plugin=protoc-gen-roc="+bin,
			"--proto_path="+path.String(),
			"--roc_out="+output.String(),
			protobuf,
		)

		executor.Run()
	},
}

func init() {
	genCmd.Flags().StringP("path", "i", "", "The proto path. (dirname(protobuf file)).")
	genCmd.Flags().StringP("output", "o", "", "The output dir.")
}
