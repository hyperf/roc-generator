package cmd

import (
	"fmt"
	"github.com/spf13/cobra"
)

var genCmd = &cobra.Command{
	Use:   "gen:roc",
	Short: "Generate protoc command",
	Long:  `Generate protoc command which used to generate structs and interfaces by protobuf.`,
	// Uncomment the following line if your bare application
	// has an action associated with it:
	Run: func(cmd *cobra.Command, args []string) {
		fmt.Println(1234)
	},
}
