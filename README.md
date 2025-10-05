# sudoku
Basic Sudoku solvers.

A common problem whenever starting to learn a new language is that it's a pain to come up with something to do.

So this is my "Doug implements the same thing in many different languages"

It's actually kind of a nice problem to solve as it involves a bunch of base programming concepts
that are very useful to know in each language:
- basic maths (floor)
- basic OO (grid)
- maps/dictionaries/arrays (based on the language)
- byref in the relevant languages
- recursion
- loops
- control flow (if's :P)
- file io
- argv

## How do I write an implementation?

### Socket Handling

Benchmarking is done by running a server socket on `/tmp/sudoku.sock`.
It expects to receive a message that looks lke `language:name` - after which it will start sending out partially solved sudoku's.

An implementation should take the 81 bytes of the sudoku - solve it, then return the solved sudoku (one long character string, no new lines).

After receiving it, the server will send another sudoku.

We judge speed based on the number of solves in a given 10 second period.

### Debug Handling

As this would be a pain to debug, your code should also accept 81 sudoku characters as string input in STDIN.

If this is set, it should instead solve this, then print it out.

## Running Benchmarking

Each implementation should live in it's own separate folder, and be triggered by a Makefile running `make run`

The simplest way to execute this is is by running `make update`

The more complicated way is to run the server with: `make server`, then either run all the implementations with `make run` in the root directory or 
You can run the background server with `make server`. You can automatically run every implementation by running `make run` in the root directory.


## Potential languages to still do:

We have a whole bunch of languages within old, but I'm slowly converting these to be in the "new" format using unix sockets
as the old version (exec) was incredibly inaccurate due to noise

~- Ruby~
- C
- C#
- LUA
- Kotlin
- Java (lol)
- Swift
- Something in Lisp maybe?
- C++?
- Fortran
- Cobol
- Pure BASH maybe?
- ZIG
- Elixir
- Lisp of some kind (hahaha probably not)


```
SELECT
language,
name,
sum(case example_id when 1 then node_duration else 0 end) as example_1,
sum(case example_id when 2 then node_duration else 0 end) as example_2,
sum(case example_id when 3 then node_duration else 0 end) as example_3,
sum(case example_id when 4 then node_duration else 0 end) as example_4,
sum(case example_id when 5 then node_duration else 0 end) as example_5,
avg(node_duration) as example_duration
FROM implementations
INNER JOIN executions ON (executions.implementation_id=implementations.id)
WHERE result=1
GROUP BY implementations.id
ORDER BY example_duration desc;
```