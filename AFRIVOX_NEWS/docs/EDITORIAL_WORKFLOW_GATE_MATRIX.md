# Editorial workflow and gate matrix

| Transition | Authorized role/capability | Mandatory gates |
|---|---|---|
| Draft → Submitted | Author with `workflow.submit` | Valid article metadata; server-side ownership |
| Submitted → Under review | Editorial reviewer | Active account/role; conditional lock succeeds |
| Review → Fact/legal pending | Editorial reviewer | Specialist state selected; generic reviewer cannot record specialist result |
| Review/pending → Revision requested | Reviewer/fact/legal specialist | Explanation of at least 10 characters |
| Review/pending → Approved | `workflow.approve`, actor is not author | No failed checks; required/high-risk fact/legal/source/fairness/right-of-reply checks; encrypted classified source data; every linked media asset approved/unexpired; media-rights check passed |
| Approved → Scheduled | `workflow.publish`, recorded approver exists | Future schedule; all publication gates revalidated; critical actor differs from author and approver |
| Approved/Scheduled → Published | `workflow.publish`, recorded approver exists | All gates revalidated at execution time; scheduled actor is active and still authorized; critical separation |
| Published → Unpublished/Retracted | `workflow.publish` | Explanation; transition retained; retraction timestamp/correction metadata retained |
| Any retained state → Archived where allowed | `workflow.publish` | Explanation and history retained |

Exact specialist permissions are documented in `RBAC_MATRIX.md`. `lock_version` rejects stale article saves and workflow transitions. Media approval separately requires a different uploader/approver.
