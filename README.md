# Movie Library Assignment

## Environments

| Environment | Branch  | URL                 | Hostname            |
|-------------|---------|---------------------|---------------------|
| Development | develop | https://example.com | develop.example.com |

## Development Workflow

Refer [DEVELOPMENT.md](DEVELOPMENT.md).

### Skeleton Guide

Please read the skeleton repo guide to understand the structure of repo: [SKELETON-GUIDE.md](./SKELETON-GUIDE.md)

### Code Review Checklist

- Make sure [Coding Standards and Best Practices](https://learn.rtcamp.com/courses/wordpress-development-basics/l/coding-standards-and-best-practices/) followed.
- There should not be any PHPCS feedback comment on PR by rtBot.
- Make sure sanitization and escaping followed and applied correctly. For example, `esc_html()` should not be used to escape HTML tag attributes.
- Make sure plugin and theme I18N ready. For example, all the static strings displayed to user should be transtable using `__()` or other similar functions and correct text-domain used.
- Make sure PHP namespace and autoloading implemented.
- Make sure OOP concept followed.
- Make sure inline documentation added.
- Make sure PR title and description is descriptive.
- Give feedback on logical reasoning, suggest better/optimum solution and approaches.
- Do not give feedback on plugin & theme files/folder architecture.
- Try to avoid personal preferences/like/dislike in code review. For example, if there is better way to achieve something and current way is also fine then in those cases, you can give suggestions instead of feedback.
