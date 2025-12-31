# Merge help for Content Block updates

If the Content Block PR cannot merge due to conflicts, follow these steps locally:

1. Ensure the local branch is up to date with `main`:
   - `git checkout work`
   - `git fetch origin`
   - `git merge origin/main`

2. When conflicts appear in the Content Block assets, prefer the changes from the feature branch ("ours") so the row layout selector, conditional logic, and styling fixes remain intact.
   - Files to prioritize:
     - `acf-json/group-ai-chatbot-article-builder.json`
     - `assets/css/style-article-sections.css`
     - `template-parts/article-sections.php`
     - `aichatbotfree/acf-json/group-ai-chatbot-article-builder.json`
     - `aichatbotfree/assets/css/style-article-sections.css`
     - `aichatbotfree/template-parts/article-sections.php`

3. After resolving conflicts, run `git status` to confirm everything is staged, then commit the resolution:
   - `git add <files>`
   - `git commit`

4. Push the updated branch and retry the merge.

GitHub web editor reminder
--------------------------
- Clicking "Accept incoming change" or "Accept both" does **not** update the branch by itself.
- You must scroll down, click **Mark as resolved**, then **Commit merge** (or create a new commit) so the branch contains the resolved file.
- Finally, push the branch (or let the web UI commit directly to the branch) before re-attempting the merge.

If you still hit merge blockers, consider rebasing instead of merging:

```bash
git checkout work
git fetch origin
git rebase origin/main
```

Resolve conflicts the same way (preferring the Content Block updates), continue the rebase with `git rebase --continue`, then push with `git push --force-with-lease`.
