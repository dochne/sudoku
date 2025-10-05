export SUDOKU

ARGS_FOR_RUN := $(filter-out run,$(MAKECMDGOALS))
ARGS_FOR_TEST := $(filter-out test,$(MAKECMDGOALS))
.DEFAULT_GOAL := run

.PHONY: run test update build-server server

test:
	SUDOKU=070000043040009610800634900094052000358460020000800530080070091902100005007040802 make run $(ARGS_FOR_TEST)

run:
	@for mkfile in $$(find src$(if $(ARGS_FOR_RUN),/$(ARGS_FOR_RUN),) -type f -name "Makefile"); do \
		dir=$$(dirname "$$mkfile"); \
		if grep -q -E '^\s*run\s*:' "$$mkfile"; then \
			echo "\nExecuting $$dir..."; \
			$(MAKE) -C "$$dir" run; \
		fi \
	done

%:
	@:

build-server:
	cargo build --manifest-path ./server/Cargo.toml --release --target-dir ./server/bin

server:
	make build-server
	server/bin/release/server

update:
	@echo "--- Building server ---"; \
	$(MAKE) build-server; \
	echo "--- Starting server in background ---"; \
	./server/bin/release/server & \
	PID=$$!; \
	echo "Server started with PID: $$PID"; \
	echo "Waiting 5 seconds for server startup..."; \
	sleep 5; \
	echo "--- Running client process (make run) ---"; \
	$(MAKE) run; \
	echo "--- Client process finished. Waiting 5 seconds before cleanup... ---"; \
	sleep 5; \
	echo "--- Killing server process (PID: $$PID) ---"; \
	kill $$PID 2>/dev/null || echo "Server process already stopped"
