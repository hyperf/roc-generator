package cmd

import (
	"fmt"
	"github.com/hyperf/roc-generator/roc-go/config"
	"github.com/spf13/cobra"
)

var versionCmd = &cobra.Command{
	Use:  "version",
	Long: `Print the version of roc-go.`,
	// Uncomment the following line if your bare application
	// has an action associated with it:
	Run: func(cmd *cobra.Command, args []string) {
		c := config.GetRocConfig()

		fmt.Println("Version: " + c.Version)
	},
}
