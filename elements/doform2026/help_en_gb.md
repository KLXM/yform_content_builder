# DoForm2026 – Editor's Guide

DoForm2026 is an interactive form builder for editors. Forms are configured in five clear steps — no programming knowledge required.

---

## Step 1: Start

| Field | Description |
|-------|-------------|
| **Form Headline** | Visible heading displayed above the form. |
| **Headline HTML Tag** | Semantic level: H2 for main forms, H3/H4 for sub-sections. |
| **Intro Text** | Optional explanatory text shown before the fields (e.g. processing time notice). |

---

## Step 2: Fields

Define the form fields here. Use **"Add field"** to add new rows; use the arrow buttons to change the order.

### Available Field Types

| Type | Use case |
|------|----------|
| **Text** | Name, subject, free text – single-line input |
| **E-Mail** | Validates e-mail format automatically |
| **Phone** | For phone numbers, mobile-optimised |
| **Textarea** | Multi-line input, e.g. for messages |
| **Select (Dropdown)** | Predefined options to choose from |
| **Checkbox** | Single tick, e.g. for consent |
| **Radio Buttons** | Single choice from multiple visible options |
| **File Upload** | Allows attachments (configurable file types) |
| **Customer Number** | Text field with customer number validation |
| **Meter Reading** | For meter-reading forms (comma/dot normalisation) |
| **Hidden** | Not shown; transfers value invisibly |
| **Fieldset (Group)** | Opens a visual group (use together with Fieldset End) |
| **Fieldset End** | Closes a fieldset group |
| **Sub-headline** | Visually separates form sections with a heading |
| **Divider** | Inserts a horizontal rule as a separator |

### Advanced Options per Field

Click the **"Advanced options"** button on a field to reveal additional settings:

**Validation type** – defines the pattern used to validate the field:

| Type | When to use |
|------|------------|
| None | Required-field check is sufficient |
| IBAN | Bank account numbers |
| BIC/SWIFT | International bank codes |
| Postcode (DE/AT/CH) | Postcodes per country |
| Phone number | Phone format check |
| Date (DD.MM.YYYY) | German date format |
| Date (YYYY-MM-DD) | ISO date format |
| Time (HH:MM) | Time format |
| Numbers only | Pure digit fields |
| Letters only | No numbers allowed |
| Letters and numbers | Alphanumeric |
| Min length | Combine with Validation parameter (number) |
| Max length | Combine with Validation parameter (number) |
| **Simple rule** | For custom patterns like customer numbers (see below) |
| Custom regex | For developers: full regex pattern |

---

### Simple Rule – Placeholder Notation

With validation type **"Simple rule"** editors can define custom patterns without programming. Enter the pattern in the **Validation parameter** field.

**Placeholders:**

| Character | Meaning |
|-----------|---------|
| `A` | Exactly 1 letter (a–z, A–Z) |
| `9` | Exactly 1 digit (0–9) |
| `*` | Letter or digit |
| Number (e.g. `30000`) | Maximum value (for pure numeric values only) |
| Any other character | Fixed character (hyphen, slash, space …) |

**Examples:**

| Pattern | Matches | Does not match |
|---------|---------|----------------|
| `KD-30000-99-AA` | KD-12345-67-AB | KD-99999-00-12 |
| `DE99 9999 9999 9999 9999 99` | DE89 3704 0044 0532 0130 00 | DE1234 |
| `AAA-99999` | ABC-12345 | AB-123 |
| `9999` | 1234 (≤ 9999) | 12345 |

> **Tip:** Letters are automatically converted to upper case if you choose "Force case" → **Letters/numbers (UPPER)**.

---

**Force case** – automatically transforms the input:

| Option | Effect |
|--------|--------|
| Trim spaces | Removes leading/trailing spaces |
| UPPERCASE | Converts all characters to upper case |
| lowercase | Converts all characters to lower case |
| Remove all spaces | No spaces allowed (e.g. IBAN) |
| Digits only | Removes all non-digit characters |
| Letters/numbers (UPPER) | Removes special characters, uppercases result |
| Normalise meter reading | Converts dot to comma (1.234 → 1,234) |

---

## Step 3: Mail

| Field | Description |
|-------|-------------|
| **Recipient e-mail** | Where the form submission is sent. Separate multiple addresses with comma or semicolon. |
| **E-mail subject** | Subject line of the notification e-mail. Placeholders: `{name}`, `{email}`, `{subject}` |
| **Sender** | "E-mail from form" uses the user's address as sender. "System e-mail" uses the configured system address. |
| **Success message** | Text shown to the user after successful submission. |
| **Error message** | Text shown on technical failure (e.g. mail server unreachable). |

---

## Step 4: Security

### Spam Protection

| Option | How it works |
|--------|-------------|
| **Honeypot** | An invisible field – only bots fill it in. No effort for real users. |
| **Time check** | The form must be open for at least 3 seconds. Bots are usually faster. |
| **Both** | Recommended combination for maximum protection. |
| None | Only for internal or non-public pages. |

### Privacy Checkbox

When enabled, the user must explicitly consent before submitting.

- **Privacy text**: Text next to the checkbox. Use `{link}` as placeholder for the linked term.
- **Privacy link**: URL to the privacy policy page (e.g. `/privacy/`).

**Example:** `I have read the {link} and accept it.`  
→ "I have read the [Privacy Policy](/privacy/) and accept it."

### Copy to Sender

| Field | Description |
|-------|-------------|
| **Enable copy** | Sends the sender a copy of their submission |
| **Copy subject** | Subject line of the confirmation e-mail |
| **Copy intro** | Opening text of the confirmation e-mail |
| **Copy footer** | Closing text (signature, company contact details) |
| **Mask IBAN** | Protects sensitive bank data in the copy (e.g. DE** **** **** **** **** 00) |

---

## Step 5: Layout

### Basic Settings

| Field | Description |
|-------|-------------|
| **Layout** | **Default**: fields stacked vertically. **Grid**: fields side by side according to each field's width setting. |
| **Button style** | Colour and style of the submit button (primary, secondary, danger etc.) |
| **Container width** | Maximum width of the form block (e.g. `uk-container-small`) |
| **Section padding** | Vertical spacing around the form block |

### AJAX Enhancement

When enabled, the form is submitted without a full page reload. The success or error message appears directly within the form block.

### Multi-step Form

Splits the form into multiple pages. Each **Fieldset** becomes its own step. Users can navigate forwards and backwards between steps.

| Field | Description |
|-------|-------------|
| **Enable multi-step** | Activates multi-step behaviour |
| **Back button label** | Label of the "Back" button (default: "Back") |
| **Next button label** | Label of the "Next" button (default: "Next") |

> **Tip for multi-step:** Structure your fields using **Fieldset** elements. Each fieldset block (Fieldset to Fieldset End) becomes one form step.

---

## Frequently Asked Questions

**Why is no e-mail arriving?**  
Check that a recipient address is entered in Step 3. Also check the spam folder of the mailbox.

**Can fields appear side by side?**  
Yes – choose "Grid" layout in Step 5 and set each field's width (e.g. "Half width" to place two fields next to each other).

**How do I build a multi-step form?**  
Add **Fieldset** elements as separators. Enable "Multi-step" in Step 5. Each fieldset block will become its own step.

**Can I use custom validation patterns?**  
Yes – choose "Simple rule" as the validation type and enter the pattern in the "Validation parameter" field (see placeholder table above).
