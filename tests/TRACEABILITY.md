# Acceptance-Criterion Traceability

Every automated test must reference its requirement in the test name or a nearby docblock. The directory is organized by epic and user story, with separate unit, integration, and acceptance levels.

| Story | Requirement coverage | Test locations |
| --- | --- | --- |
| US-01 Create an Account | AC-01 through AC-08 | `tests/epic01/us01/` |
| US-02 Log into account | AC-01 through AC-06 | `tests/epic01/us02/` |
| US-03 Log Out | AC-01 through AC-05 | `tests/epic01/us03/` |
| US-04 Search for movies | AC-01 through AC-11 | `tests/epic02/us04/` |
| US-05 Add a movie | AC-01 through AC-08 | `tests/epic02/us05/` |
| US-06 View my watchlist | AC-01 through AC-06 | `tests/epic02/us06/` |
| US-07 Filter my watchlist | AC-01 through AC-06 | `tests/epic02/us07/` |
| US-08 Update a watchlist item | AC-01 through AC-10 | `tests/epic02/us08/` |
| US-09 Remove a movie | AC-01 through AC-05 | `tests/epic02/us09/` |
| US-10 Create a group | AC-01 through AC-07 | `tests/epic03/us10/` |
| US-11 Join a group | AC-01 through AC-07 | `tests/epic03/us11/` |
| US-12 View group activity | AC-01 through AC-08 | `tests/epic03/us12/` |
| US-13 Leave a group | AC-01 through AC-05 | `tests/epic03/us13/` |
| US-14 Delete a group | AC-01 through AC-06 | `tests/epic03/us14-delete/` |
| US-14 Vote on a film | AC-01 through AC-08 | `tests/epic03/us14-vote/` |
| US-15 Usable error handling | AC-01 through AC-04 | `tests/epic04/us15/` |
| US-16 Maintainable structure | AC-01 through AC-06 | `tests/epic04/us16/` |
| US-17 Secure user data | AC-01 through AC-05 | `tests/epic04/us17/` |
| US-18 Application availability | AC-01 through AC-04 | `tests/epic04/us18/` |

A criterion is not considered covered until a passing test exists with an explicit `test_usXX_acYY_...` name or an equivalent traceability annotation.
