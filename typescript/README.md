# Typescript

## Gotchas

There's nothing that difficult about NodeJS/Typescript, here are my minor gotchas:
- Typescript files have an extension of .ts
- It doesn't by default recognise nodejs built in imports, so requires the `@types/node` dependency
- `.fill` only runs once, so if you return an object then it'll return a reference to the same object 

## Resources:

The tslint + tsconfig code was copied from here:
- https://developer.okta.com/blog/2018/11/15/node-express-typescript