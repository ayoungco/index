- [x] Remove the additional auth0 button on the landing page
- [x] Turn the light/dark mode button into that iOS-style toggle
- [x] embellish the visual design with "atomic heart"-style soviet futurism terminal UI, keeping the same color scheme but adding glare, aberration, and rounding corners on borders. it should look like 

- [ ] Allow anonymous access by recording origin IP and expressing browser agent as "anonymous user from [city, country] using [browser]". Add access logs to the object timeline, showing when it was accessed and by whom (if authenticated) or by an anonymous user (with the above info). This will help users see how their objects are being used and accessed over time. Show a flag emoji for origin
- [ ] add a "timeline" view to each object, showing all the interactions and changes that have happened to it over time. This could include things like when it was created, when it was accessed, when it was modified, and by whom. This would give users a better understanding of how their objects are being used and how they have evolved over time.



- [ ] implement anonymous usage that stores IP and user agent, representing them on the timeline as "anonymous user from [city, country] using [browser]". Add access logs to the object timeline, showing when it was accessed and by whom (if authenticated) or by an anonymous user (with the above info). This will help users see how their objects are being used and accessed over time.


- [ ] Stripe monetization: users can press a prominent "upgrade" button to get a subscription, which will give them access to the "pro" features. This will be a one-time payment, not a recurring subscription. Weight cost benefit of adding Laravel Cashier or just implementing Stripe's API directly.1