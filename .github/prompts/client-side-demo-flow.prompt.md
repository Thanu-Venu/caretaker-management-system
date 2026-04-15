---
description: "Generate a full client-side presentation demo flow from login to completion"
name: "Client Side Demo Flow"
argument-hint: "Optional focus (for example: booking only, payment path, recurring payments, complaint flow)"
agent: "agent"
---
Analyze this workspace and produce an end-to-end client journey for presentation demo use.

Focus on the client side flow from scratch to completion, including:
- account registration path (if implemented), then login entry points
- client dashboard sections and what each section is used for
- booking path (select service, fill booking form, submit)
- available caregiver discovery and selection behavior
- booking confirmation and status progression
- payment path (advance payment, checkout, confirmations, recurring installments if available)
- post-booking actions (upcoming bookings, profile updates, complaints, feedback, notifications)

Use actual code behavior from controllers, views, routes, and helpers. Do not assume features that are not implemented.

Return the result in this exact structure:
1. Demo storyline summary (5 to 8 sentences)
2. Step-by-step client flow (detailed click-by-click)
3. Screen-by-screen talk track for presentation (include exact user actions per step)
4. Required demo data and pre-demo setup checklist
5. Alternate branches and fallback paths (errors, missing caregivers, payment failure, validation issues)
6. Key files map (file path plus one-line purpose)
7. Known gaps or uncertain areas that should be verified before demo

Style requirements:
- Keep language presentation-friendly and non-technical where possible
- Include button names, menu items, and expected user actions
- Use clear stage labels such as "Stage 1", "Stage 2", and so on
- Prefer chronological order and indicate decision points explicitly
