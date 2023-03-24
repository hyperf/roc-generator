package cmd

import (
	"github.com/spf13/cobra"
	"os"
	"os/exec"
)

var genCmd = &cobra.Command{
	Use:   "gen:roc [file to print]",
	Short: "Generate protoc command",
	Long:  `Generate protoc command which used to generate structs and interfaces by protobuf.`,
	// Uncomment the following line if your bare application
	// has an action associated with it:
	Run: func(cmd *cobra.Command, args []string) {
		bin, err := os.Executable()
		if err != nil {
			panic(err)
		}

		executor := exec.Command(
			"protoc",
			"--plugin=protoc-gen-roc="+bin,
			"--proto_path=.",
			"--roc_out=.",
			"example.proto",
		)

		executor.Run()
	},
}
