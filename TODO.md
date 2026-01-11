# TODO - index

- [ ] feature: https://index.ayoung.co/{slug} route that looks up if a Thing  exists, accepting any valid URL string. if it is not found, throw 404 and ask the user to log in or register to create the thing. if it is found, display information about the thing.

Example: I have placed labels with QR codes on my lab equipment, linking to index.ayoung.co/{equipment-serial-number}. when scanned, the link should take the user to a page about that specific piece of equipment, if it exists in the database. if it doesn't there should be an option to register the equipment.

- [ ] use docs/context/camera.md to implement camera access for image capture of thing QR label or QR code scanning to capture images of the thing. only display 

# DEFERRED

- [ ] Welcome landing CTA should be a giant search bar, with a tagline like "Find anything in the world of science and standards". or a giant green "GO" button that focuses the search bar.