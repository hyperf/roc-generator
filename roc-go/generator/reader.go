package generator

import (
	"bufio"
	"io"
	"os"
	"time"
)

func read(ch chan []byte) {
	in := bufio.NewReader(os.Stdin)
	result, err := io.ReadAll(in)
	if err == nil {
		ch <- result
	}
}

func ReadStdin() []byte {
	ch := make(chan []byte, 1)
	go read(ch)
	var ret []byte
	select {
	case ret = <-ch:
		break
	case <-time.After(time.Second):
		break
	}

	return ret
}
