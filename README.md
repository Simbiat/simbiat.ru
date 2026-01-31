Code of https://www.simbiat.eu website

Some core principles/conventions tried to be followed when coding and designing:

- Security at the forefront, even when it can overcomplicate things.
- Privacy after security: collect *personal* user data only if it can be used for something by the users (like user agents for sessions to identify and log-out active sessions). Otherwise - do not store it, and ideally do not even manipulate it.
- No ads (affiliate and partner links may be considered in future).
- No pseudo-AI nonsense.
- No "login with social network" due to potential security and privacy risks. Linking of social accounts is considered for the future to allow automated posting to those, but only through direct interaction with the main site.
- No use of ORM (like Doctrine): seemingly convenient layer, that adds extra performance and learning overhead, while obfuscating what's really happening with the database.
- No foreign key constraints: practice showed that even current website workload get a performance significant hit from those due to frequent INSERTs and UPDATEs. Necessary controls should be provided in respective portions of code. FKs are to be used only if such controls cannot be provided and existence of orphaned rows or incorrect value can result in significant service degradation and/or have an impact on security or privacy.
- Limit database tables with "types" or similar objects. Static tables like that should be defined in code (as ENUMs with optional additional logic). Use of a table is allowed if customization through UI or frequent updates are expected.
- Entity classes are to be treated mainly as "collections of atomic methods". That is they should not rely on other methods inside the same or other classes, that can result in some side effects. This *may* be allowed, if the side effect is expected to be a *result* of the main action on another entity (for example, removal of a post updates respective thread's statistics). The main goal is to limit the data being used while doing any action to absolute minimum and reduce inter-class dependencies, where it makes sense.
- In case of parent-child or rather container-content relationship between database entities, some metadata about "content" is encouraged to be stored in respective "container" row even if it can be easily derived. This is for sake of ease of access to data and performance. This, however, should be applied only for data which can be requested en-masse for multiple entities at once. Good example is information about total posts, last post date, etc. for a forum thread, since this is shown on forum pages.
- All processing logic should be part of respective entity classes, and not part of classes, that generate *views* (HTML pages, API).
- Flag for UI elements availability should be calculated on PHP level and not on Twig level.
- Naming conventions, applicable to all code:
    - Variables are expected to be in `snake_case`
    - Constants are expected to be in `SCREAMING_SNAKE_CASE`
    - Class names are expected to be in `PascalCase`
    - Function names are expected to be in `camelCase`
    - Possible exceptions:
        - Path names and URLs, at least those that aim to "replicate" other sources, for example `freecompany` may be used for FFTracker, because this is how it's spelled on Lodestone
        - Array keys that are used to render text directly, especially those that have "unique" values (like `DPS` and `PvP` for Free Company processing in FFTracker)
        - Array keys that use common abbreviations (like `IP`, `HTML`, `URL`, etc.)
        - Variables when used as part of some 3rd-party library to maintain its conventions and/or its original names (for example attributes' names from BIC library) or that are based on 3rd-party specifications (like RSS/Atom attributes)
        - `SCREAMING_SNAKE_CASE` is applied to constants in JS *only* if they are public (otherwise half the code will be screaming)
        - Class names using acronyms (for example `NL2Tag`)