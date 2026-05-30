# Spartan App Notes

Ongoing goal: keep the app spartan, easy, and effective.

- Prefer server-rendered Blade, normal links, and normal form posts.
- Use small page-local JavaScript only where it directly improves the scanning workflow, such as camera/file selection and auto-submit.
- Avoid SPA-style navigation and component runtimes unless a core workflow needs them.

## Removed Starter-Kit Surfaces

- Local email/password login, registration, password reset, password confirmation, password settings, and local account deletion were removed. Auth0 is the authentication system for this app.

## Open Decisions

- If a non-Auth0 fallback login is ever required, add it deliberately as a separate native session guard and route group. Do not mix it into the Auth0 guard.
