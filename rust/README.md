# Rust

## Gotchas

I appreciate what they've designed and created here, but Jeeesus it's a pain compared to my 
usual code monkey self. Lots of reading was involved

- As we're operating on solve recursively, every call that talks to grid needs to call it as &mut grid
regardless of whether we're actually mutating the grid object. This is because you cannot pass an object byref as
immutable AND as mutable in different places. If you could, it would no longer be able to guarantee that the object
hadn't changed to whomever it passed it as immutable to.
- `self.cells.iter().enumerate` is an example of something that will try and get an immutable ref to the grid object. As such, it doesn't play nice, see above

## Resources:

The rust lang book is pretty comprehensive:
- https://doc.rust-lang.org/book/

There's also a fantastic unofficial tutorial that gives a good overview of the problems you may come across when trying
to do the most complicated of tasks, building a linked list. Excellent resource
- https://rust-unofficial.github.io/too-many-lists/index.html


