# TODO - index

- [ ] Welcome landing CTA should be a giant search bar, with a tagline like "Find anything in the world of science and standards". or a giant green "GO" button that focuses the search bar.

## Features

- [ ] User accounts with OAuth2 login (Google, GitHub, ORCID, etc.) and profile pages.

- [ ] Large, omnipresent search/command bar that emulates the responsiveness of a command line interface. It should return AJAX results for character-by-character input, and allow for fuzzy matching.
- [ ] Each registered Thing should have a human-readable landing page at a fixed, semantically meaningful URL, with their most likely type as the first segment (e.g., `/element/oxygen`, `/compound/water`, `/standard/ISO-9001`).
- [ ] QR codes will be generated for each Thing, linking to its landing page. This will allow for easy sharing and referencing in physical documents or presentations.
- [ ] AWS style "breadcrumbs" at the top of each Thing's page, showing its hierarchical context (e.g., `Home > Element > Oxygen`).

### Installation Experience

- This application should be extremely easy to install and run locally, with a single command to set up the environment, install dependencies, and start the server. Consider using Docker for containerization to simplify the setup process. The goal is to allow users to get the application up and running with minimal effort, ideally in under 5 minutes, so they can explore its features without any hassle.