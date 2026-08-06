# Database model

## communication_campaigns

- id
- title
- channel
- status
- template_id nullable
- message
- provider
- filters_json nullable
- created_by nullable
- scheduled_at nullable
- started_at nullable
- completed_at nullable
- created
- modified

## communication_campaign_recipients

- id
- campaign_id
- external_recipient_id
- user_id nullable
- firstname nullable
- lastname nullable
- phone nullable
- email nullable
- variables_json nullable
- status
- opened_at nullable
- processed_at nullable
- failed_at nullable
- error_message nullable
- created
- modified

Recipient data is stored as a snapshot so campaign history remains readable even if the source user changes later.

## communication_templates

- id
- title
- slug
- category nullable
- channel nullable
- subject nullable
- message
- active
- created
- modified

## Initial statuses

Campaign:

- draft
- ready
- in_progress
- completed
- cancelled

Recipient:

- pending
- opened
- processed
- skipped
- failed
