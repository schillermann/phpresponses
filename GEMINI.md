# Elegant Objects & True OOP

This project follows the strict principles of **Elegant Objects**, as advocated by Yegor Bugayenko, synthesized with **Alan Kay's** original vision of Object-Oriented Programming. We do not build "systems of classes"; we build systems of autonomous, communicating organisms.

## Core Principles

### The Messaging Philosophy (Alan Kay, through EO lens)
1.  **It's All About Messaging**: "The big idea is messaging." We don't call methods; we send messages. In our strict environment, this means using interfaces. An object is a black box that responds to a contract.
2.  **Encapsulation (Cellular Biology)**: Objects are like biological cells. They protect their internal state and only communicate through messages. No leaking internals via getters.
3.  **Strict Late Binding**: We achieve flexibility not through dynamic typing (which is a mess), but through **Composition and Decorators**. We "bind" behavior at runtime by wrapping objects, maintaining strict type safety while allowing the system to evolve.

### Elegant Objects (Yegor Bugayenko)
1.  **No Null**: We never use `null`. An object is either there or it isn't. Use Null Objects.
2.  **No Code in Constructors**: Constructors must only initialize attributes. No logic, no validation.
3.  **No Getters and Setters**: Objects are not data structures. Do not ask them for data; send them a message to perform an action.
4.  **No Static Methods**: Static methods are global procedures. Everything must be an object.
5.  **No Type Casting**: If you need to cast, your design is broken. Use polymorphism.
6.  **No Implementation Inheritance**: Use interfaces and composition.
7.  **Objects are Immutable**: Once created, an object never changes. This makes them thread-safe and reliable.
8.  **Objects are Small**: Max 250 lines per class.
9.  **Objects are Final**: Every class must be `final` or `abstract`.
10. **Objects are not Data Structures**: An object is an abstraction, not a bucket of data.

## Development Workflow

### Commit Messages
All commit messages **MUST** follow the [Conventional Commits v1.0.0](https://www.conventionalcommits.org/en/v1.0.0/) specification. This ensures our history is as clean and structured as our code.

Format: `<type>[optional scope]: <description>`
