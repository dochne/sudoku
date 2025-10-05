# Sudoku

A common problem whenever starting to learn a new language is that it's a pain to come up with something to do.

So this is my "Doug implements the same thing in many different languages"

It's actually kind of a nice problem to solve as it involves a bunch of base programming concepts
- basic maths (floor)
- basic OO (grid)
- maps/dictionaries/arrays (based on the language)
- byref in the relevant languages
- recursion
- loops
- control flow
- env vars
- sockets

## How do I write an implementation?

After many years of having awful awful ways of trying to measure how fast an implementation is, I've switched it up a bit!

### Makefile

Add a makefile with a `run` method in it - this should trigger an execution
If you're feeling particularly spicy, add a `profile` method in there too. 

Any given implementation should handle two potential sources of input.

### SUDOKU env var

Crucial for when you're debugging and writing your implementation, your implementation should listen for an env variable named
SUDOKU. This will contain 81 characters of text, where the number 0 will represent a number that we do not know.

The output should be a similar 81 chars, followed by a newline.

### Socket Handling

Once you're happy your implementation works, we should instead move onto the system used for Benchmarking!

This works with a very very basic protocol onto a server we'll have running on the machine.

Your program should connect to the `/tmp/sudoku.sock` unix socket, then send `$language:$implementation` (e.g. `ruby:basic`)
The server will then start sending sudoku's at you to solve (in the form of 81 characters of text).
You should respond with the answer - at which point you should listen again as it'll send you the next sudoku.

Benchmarks are saved in a database locally named `var/database.db` which will then update `benchmark.md`

## Execution


- `make server` will start the background server
- `make run` will trigger running *every* implementation
- `make run rust` will trigger every rust implementation
- `make run rust/basic` will run the rust/basic implementation
- `make update` will automatically start the server, then run `make run`

- `make test` will trigger *every* implementation with a SUDOKU env var
- `make test rust` ...you get the idea


## Languages:

There's a few languages/implementations still to port over - but here's a bunch of ideas

~- Ruby~
- C
- C#
- LUA
- Kotlin
- Java
- Swift
- Something in Lisp maybe?
- C++?
- Fortran
- Cobol
- Pure BASH maybe?
- ZIG
- Elixir
- Lisp of some kind (hahaha probably not)
