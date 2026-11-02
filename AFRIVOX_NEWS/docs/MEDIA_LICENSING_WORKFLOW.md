# Media licensing and publication workflow

1. An authorized uploader supplies the file plus title, accessible alt text, creator, source organization, license, copyright holder, expiry and usage restrictions.
2. The server validates upload status/size, real MIME, decoded dimensions and pixel budget. PHP GD decodes and re-encodes a JPEG derivative. Both original and derivative remain outside the public web root in quarantine.
3. A malware scanner records `clean` before production approval. `malware_scanner_required=true` makes approval fail closed when this result is absent.
4. A different authorized multimedia/rights reviewer checks provenance, license scope, territory/channel restrictions, expiry, caption and accessibility. The uploader cannot approve their own asset.
5. Expired licenses, missing metadata, pending/infected scans and restrictions such as “not for publication” block approval.
6. Only after approval does the server copy the safe derivative into `uploads/` and update the linked article. Originals never become public.
7. Article approval/publication separately requires every linked asset to remain approved/unexpired and a passed `media_rights` specialist check.
8. Approval, rejection and associated article workflow actions are audited. Rejections require a reason and the file remains private.

The publication must approve its license taxonomy, evidence/contract retention period and takedown procedure before production (`BLOCKED_EXTERNAL`). A scanner integration and positive GD/scanner E2E are also launch blockers in the current environment.
