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

## Todo:
This kind of microbenchmark is so... micro'y, that it isn't really helpful. I think an interesting change would be to instead
write a "sudoku server" that binds to a unix port. The conversation would look like this:
1. Client -> Server: "Hi! I'm typescript:bruteforce!"
2. Server -> Client: 3243262234324330432433 (sudoku to solve)
3. Client -> Server: 3243262234324330432433 (solved sudoku)
4. if client is wrong, kill connection
5. if client is right, continue ad nauseum until X time has passed (say, 10 seconds) then we rate it as sudokus per second

## Potential languages to still do:

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