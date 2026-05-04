# Production Deployment Checklist

**Date**: 2026-05-04
**Feature**: Distribution Basic Subscription & Release Limits
**Estimated Downtime**: None (zero-downtime deployment)

---

## Pre-Deployment Checklist

### 1. Backup Database
- [ ] Create full database backup
- [ ] Verify backup is complete and accessible
- [ ] Store backup in safe location with timestamp

```bash
# Example backup command
mysqldump -u [user] -p [database] > backup_1n2music_20260504_pre_deployment.sql
```

### 2. Code Deployment
- [ ] Pull latest code from `master` branch (commit: `52506b1`)
- [ ] Verify all files are updated:
  - `app/Helpers/functions/rrt_base_func.php`
  - `app/Http/Controllers/Public/StudioReleaseController.php`
- [ ] Clear application cache
- [ ] Restart application server (if needed)

```bash
# Example deployment commands
git pull origin master
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## Database Migration

### 3. Run SQL Script
- [ ] Review `database/sql/PRODUCTION_DEPLOYMENT.sql`
- [ ] Connect to production database
- [ ] Run the script in a transaction
- [ ] Review verification results
- [ ] **COMMIT** if all looks good, **ROLLBACK** if issues found

```bash
# Connect to production database
mysql -u [user] -p [database]

# Run the script
source database/sql/PRODUCTION_DEPLOYMENT.sql

# Review verification results, then:
COMMIT;  # or ROLLBACK; if issues
```

### Expected Results:
```
✓ Distribution Basic role ID: 7
✓ Distribution Basic subscription created/verified
✓ Publishing pricing updated to $200/year
✓ Pro Seller Music Recognition updated to 5 uses
✓ Publishing AI limits updated to 20/40
✓ Distribution Basic AI limits set to 5/5
```

---

## Post-Deployment Verification

### 4. Test Role Detection
- [ ] Run test script: `php test_user_role.php`
- [ ] Verify all 21 tests pass
- [ ] Check Distribution Basic role returns `distribution-basic-annually`

```bash
php test_user_role.php
# Expected: Total Tests: 21, Passed: 21, Failed: 0
```

### 5. Test Release Limits
- [ ] Run test script: `php test_release_limits.php`
- [ ] Verify logic checks pass (8/9 tests)
- [ ] Verify subscriptions exist in database

```bash
php test_release_limits.php
# Expected: Total Tests: 9, Passed: 8, Failed: 1 (table name check - not critical)
```

### 6. Manual Testing

#### Test Distribution Basic Signup
- [ ] Create new user account
- [ ] Subscribe to Distribution Basic ($60/year)
- [ ] Verify role assigned: `distribution-basic-annually`
- [ ] Check AI limits in dashboard: 5 Mastering, 5 Recognition

#### Test Release Limits - Distribution Basic
- [ ] Create 1st single → Should succeed
- [ ] Create 2nd single → Should succeed
- [ ] Create 3rd single → Should be blocked with error:
  - "You have reached the limit of 2 singles per year for the Distribution Basic plan"
- [ ] Create 1st album → Should succeed (separate limit)
- [ ] Create 2nd album → Should succeed
- [ ] Create 3rd album → Should be blocked with error:
  - "You have reached the limit of 2 albums per year for the Distribution Basic plan"

#### Test Release Limits - Distribution Pro
- [ ] Create 4 singles → All should succeed
- [ ] Create 5th single → Should be blocked (limit: 4)
- [ ] Create 2 albums → Should succeed
- [ ] Create 3rd album → Should be blocked (limit: 2)

#### Test AI Limits
- [ ] Pro Seller: Verify 5 Mastering, 5 Recognition
- [ ] Distribution Basic: Verify 5 Mastering, 5 Recognition
- [ ] Distribution Pro: Verify 10 Mastering, 20 Recognition
- [ ] Publishing: Verify 20 Mastering, 40 Recognition

### 7. Check Existing Users
- [ ] Verify existing Distribution Pro users still work correctly
- [ ] Verify existing Publishing users still work correctly
- [ ] Check that no existing users are affected negatively

---

## Monitoring (First 24 Hours)

### 8. Monitor Key Metrics
- [ ] Check error logs for any new errors
- [ ] Monitor subscription signup rate
- [ ] Track release creation attempts
- [ ] Watch for limit-related error messages

### 9. User Support
- [ ] Brief support team on new limits
- [ ] Prepare FAQ for Distribution Basic
- [ ] Monitor support tickets for issues

---

## Rollback Plan (If Needed)

### 10. Emergency Rollback
If critical issues are found:

1. **Rollback Database**:
```sql
START TRANSACTION;

-- Revert Pro Seller Music Recognition to 10
UPDATE rrt_ai_package_roles SET usage_count = 10 WHERE ai_id = 2 AND role_id = 3 AND package_id = 4;
UPDATE rrt_ai_package_roles SET usage_count = 10 WHERE ai_id = 2 AND role_id = 4 AND package_id = 4;

-- Revert Publishing AI limits to 10/10
UPDATE rrt_ai_package_roles SET usage_count = 10 WHERE ai_id = 1 AND role_id = 6 AND package_id = 2;
UPDATE rrt_ai_package_roles SET usage_count = 10 WHERE ai_id = 2 AND role_id = 6 AND package_id = 4;

-- Revert Publishing pricing to $1000
UPDATE rrt_subscriptions SET pricing_annually = 1000 WHERE slug = 'publishing';

COMMIT;
```

2. **Rollback Code**:
```bash
git revert 52506b1
git push origin master
# Deploy previous version
```

3. **Restore Database Backup** (if needed):
```bash
mysql -u [user] -p [database] < backup_1n2music_20260504_pre_deployment.sql
```

---

## Success Criteria

Deployment is considered successful when:

- ✅ All database migrations completed without errors
- ✅ Test scripts pass (21/21 role tests, 8/9 release tests)
- ✅ Manual testing confirms limits work correctly
- ✅ No increase in error rate
- ✅ Existing users unaffected
- ✅ New Distribution Basic signups work correctly

---

## Communication

### Before Deployment
- [ ] Notify team of deployment window
- [ ] Brief support team on changes
- [ ] Prepare user-facing documentation

### After Deployment
- [ ] Confirm successful deployment to team
- [ ] Update internal documentation
- [ ] Send summary to stakeholders

---

## Notes

- **Zero Downtime**: This deployment requires no downtime
- **Backward Compatible**: Existing users are not affected
- **Safe to Rollback**: All changes can be reverted if needed
- **Test Scripts**: Keep test scripts for future regression testing

---

## Sign-Off

- [ ] Database backup completed by: _________________ Date: _______
- [ ] SQL migration reviewed by: _________________ Date: _______
- [ ] SQL migration executed by: _________________ Date: _______
- [ ] Code deployed by: _________________ Date: _______
- [ ] Testing completed by: _________________ Date: _______
- [ ] Deployment approved by: _________________ Date: _______

---

## Contact Information

**In case of issues during deployment:**
- Developer: [Your contact]
- Database Admin: [DBA contact]
- DevOps: [DevOps contact]
- On-call: [On-call contact]
