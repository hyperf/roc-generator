package generator

import (
	"go/ast"
	"go/parser"
	"go/token"
	"os"
)

func parse() {
	code, _ := os.ReadFile("demo/User.go")
	fset := token.NewFileSet()
	f, _ := parser.ParseFile(fset, "", code, 0)
	_ = ast.Print(fset, f)
}
