package generator

import (
	"bufio"
	"os"
	"time"
)

func read(ch chan string) {
	in := bufio.NewReader(os.Stdin)
	result, err := in.ReadString('\n')
	if err == nil {
		ch <- result
	}
}

func ReadStdin() []byte {
	ch := make(chan string, 1)
	go read(ch)
	str := ""
	select {
	case str = <-ch:
		break
	case <-time.After(time.Second):
		break
	}

	return []byte(str)
}
