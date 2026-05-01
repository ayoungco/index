# index
# Design Document

This document is a living design document for index. It is intended to be a high-level overview of the system, its architecture, and its design decisions. It is not intended to be a detailed technical specification, but rather a guide for developers and stakeholders to understand the system and its goals.

## Function

- mobile-first as a scanner tool.
- desktop experience should be a powerful search and reference tool.
- urls should be dead simple and human readable

## Visual

- everything the user sees should be minimal and clear, with a focus on the content and functionality. utilitarian but cool lightweight and bold terminal aesthetic.

### Reproducible QR label

- The scanned Item page and print page must render the same minimum QR label partial.
- The label is brutally simple: index logo, QR code, line break, then an inverted bold title block.
- No decorative spacing, no borders, and no alternate print-only composition. If the label changes, change the shared Blade partial.

### Influence

- Atomic Heart UI
- UI from Deus Ex: Mankind Divided 
- Cyberpunk 2077 computer interfaces (Militech Project Cynosure)

- Triangles and hexagons iconography (the logo is an equilateral hazard triangle with an "i" for information in the center, and the "i" is also a hexagon, so the logo is a triangle made of hexagons, and the hexagons are made of triangles, etc.)~~~
