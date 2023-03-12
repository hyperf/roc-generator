package generator

import (
	"go/ast"
	"go/parser"
	"go/token"
	"os"
)

func Parse(file string) (fset *token.FileSet, f *ast.File, err error) {
	code, err := os.ReadFile(file)
	if err != nil {
		return nil, nil, err
	}

	fset = token.NewFileSet()
	f, err = parser.ParseFile(fset, "", code, 0)
	if err != nil {
		return nil, nil, err
	}

	return fset, f, nil
}
