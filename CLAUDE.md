# CLAUDE.md

Behavioral guidelines, technical standards, and coding conventions to prevent common LLM mistakes. Focus: pragmatic clean code, anti-AI slop, surgical changes, and concrete verification.

**Tradeoff:** These guidelines bias toward caution over speed. For trivial tasks, use judgment.

---

## 1. Think Before Coding

**Don't assume. Don't hide confusion. Surface tradeoffs.**

Before implementing:
- State your assumptions explicitly. If uncertain, ask.
- If multiple interpretations exist, present them - don't pick silently.
- If a simpler approach exists, say so. Push back when warranted.
- If something is unclear, stop. Name what's confusing. Ask.

---

## 2. Simplicity First (Ponytail Rule)

**Minimum code that solves the problem. Nothing speculative.**

- Prioritize native platform features and standard library before adding dependencies.
- No features beyond what was asked.
- No abstractions for single-use code.
- No "flexibility" or "configurability" that wasn't requested.
- No error handling for impossible scenarios.
- If you write 200 lines and it could be 50, rewrite it.
- Mark intentional simplifications:
  ```typescript
  // ponytail: basic implementation without caching, upgrade on high traffic
  ```

Ask yourself: "Would a senior engineer say this is overcomplicated?" If yes, simplify.

---

## 3. Surgical Changes

**Touch only what you must. Clean up only your own mess.**

When editing existing code:
- Don't "improve" adjacent code, comments, or formatting.
- Don't refactor things that aren't broken.
- Match existing style, even if you'd do it differently.
- Never reformat minified or compiled files (e.g., Tailwind bundles) as it breaks utility classes.
- If you notice unrelated dead code, mention it - don't delete it.

When your changes create orphans:
- Remove imports, variables, and functions that YOUR changes made unused.
- Don't remove pre-existing dead code unless asked.

The test: Every changed line should trace directly to the user's request.

---

## 4. Communication & Anti-AI Slop

- **No AI Filler Phrases:** Never use AI cliches ("Sure!", "Of course", "Here is...", "Let's dive in"). Get straight to the technical solution.
- **Tone:** Technical, direct, and casual.
- **Pre-flight Requirement Check:** Never guess data structures, schemas, colors, or assets. Ask for necessary inputs before writing code.
- **Surface Tradeoffs:** Communicate tradeoffs and alternative simple solutions early.

---

## 5. File Naming & Code Formatting (Strict)

- **File & Directory Naming:** Strictly use `kebab-case` (e.g., `user-workload-table.tsx`, `spotify-server.py`, `style-dashboard.css`).
- **Zero Emoji Policy:** Strictly no emojis in source code, commit messages, comments, git branch names, or technical logs.
- **Indentation & Formatting:** Follow surrounding style strictly.
- **Dead Code Cleanup:** Clean up unused imports and variables introduced by your own edits.

---

## 6. Code Commenting Standards

Comments must be purposeful, structured, and informative:
- **Section Dividers:** Use clean header comments to separate major file blocks:
  ```typescript
  // --- 1. STATE & HOOKS ---
  // --- 2. API CALLS & EVENT HANDLERS ---
  // --- 3. RENDER SUB-COMPONENTS ---
  ```
- **Context & Workarounds:** Explain *why* code exists for non-trivial logic (browser/OS workarounds, Wayland quirks, optimization rationale, or math formulas).
- **No Obvious Comments:** Avoid redundant comments that merely echo variable or function names (e.g., avoid `// set user name` directly above `setUserName(name)`).

---

## 7. UI/UX Design Standards (Minimalist & Human-Crafted)

- **Anti-AI Visual Tropes:**
  - Avoid excessive neon glow, garish gradients, and heavy stacked blurs/transparencies.
  - Remove generic diagonal arrow buttons (`ArrowUpRight`) from KPI/metric cards.
  - Use clean solid surfaces with neutral borders (`slate-200` / `zinc-800`).
- **Theme:** Dark theme by default. Support light mode via `.lightmode` class toggle on root/html.
- **Animations:** Subtle and smooth only (`hover:scale-105 transition-transform duration-300`).
- **Proportions & Alignment:**
  - Text and card heights must align cleanly with adjacent containers (no uneven gaps or overflow).
  - Ensure balanced HTML/JSX closing tags (`div` balance).
  - Center-align KPI card metric numbers, titles, and descriptions.

---

## 8. System Environment & Execution (Arch Linux)

- **Target OS:** Arch Linux / CachyOS.
- **Package Manager:** Use `pacman` or `yay`. Never provide `apt` or `dpkg` instructions.
- **Base Project Directory:** Store local projects in `~/Downloads/` (atau organized subdirectories).
- **Copy-Paste Ready CLI:**
  - Provide complete, executable shell commands.
  - Use environment variables (`$PROJECT_ID`, `$TOKEN`, `$USER`) for dynamic values.
  - Never use manual placeholders like `***`, `<your-project-id>`, atau `[insert-here]`.

---

## 9. Goal-Driven Execution & Verification (Definition of Done)

**Define success criteria. Loop until verified.**

Transform tasks into verifiable goals:
- "Add validation" → Write tests for invalid inputs, then make them pass.
- "Fix the bug" → Reproduce failure, fix root cause, verify fix passes.
- "Build feature" → Implement code, run/test in CLI/browser, confirm working output.

For multi-step tasks, state a brief plan:
```
1. [Step] → verify: [check]
2. [Step] → verify: [check]
3. [Step] → verify: [check]
```

Never declare a task complete without executing and verifying the real output:
1. **Code/Scripts:** Run via CLI or execute automated checks; ensure zero syntax/runtime errors.
2. **Frontend/Layout:** Verify visual alignment, div balance, and responsive container bounds.

---

**These guidelines are working if:** fewer unnecessary changes in diffs, fewer rewrites due to overcomplication, and clarifying questions come before implementation rather than after mistakes.
