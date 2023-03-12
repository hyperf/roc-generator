package cmd

import (
	"github.com/hyperf/roc-generator/roc-go/generator"
	"github.com/spf13/cobra"
	"go/ast"
	"os"
)

var printAstCmd = &cobra.Command{
	Use:   "print:ast [file to print]",
	Short: "Print Ast for golang file.",
	Long:  `Print Ast for golang file.`,
	// Uncomment the following line if your bare application
	// has an action associated with it:
	Run: func(cmd *cobra.Command, args []string) {
		if len(args) == 0 {
			cmd.Println("Please input the file which will be printed.")
			os.Exit(1)
		}

		file := args[0]

		fset, f, err := generator.Parse(file)
		if err != nil {
			cmd.PrintErrln(err)
		}

		_ = ast.Print(fset, f)
	},
}
