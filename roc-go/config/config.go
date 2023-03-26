package config

import (
	_ "embed"
	"encoding/json"
)

type RocConfig struct {
	Version string `json:"version"`
}

//go:embed roc.json
var roc []byte

func GetRocConfig() *RocConfig {
	ret := &RocConfig{}
	err := json.Unmarshal(roc, ret)
	if err != nil {
		panic(err)
	}

	return ret
}
