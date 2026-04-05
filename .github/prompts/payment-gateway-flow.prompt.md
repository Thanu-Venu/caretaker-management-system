---
description: "Explain the payment gateway flow in this project"
name: "Payment Gateway Flow"
argument-hint: "Use the current workspace or selected files to trace the payment gateway flow"
agent: "agent"
---
Inspect the current project and explain the payment gateway flow end-to-end.

Use the available workspace context, open files, and any selected code to:
- identify the entry points that start a payment
- trace controller, service, and model calls involved in creating and confirming a payment
- note validation, session handling, and database updates
- identify the external gateway integration, request and response data, and callback or webhook handling
- explain how success, failure, and retries are handled
- call out the main files involved with short descriptions

Return the result as:
1. A concise overview
2. A step-by-step flow
3. A list of important files
4. Any risks, gaps, or unclear parts

If something is missing from the codebase, say so explicitly instead of guessing.
