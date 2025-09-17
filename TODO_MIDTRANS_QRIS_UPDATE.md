# Midtrans QRIS Core API Implementation

## Progress Tracking

### ✅ Completed
- [x] Analyzed current implementation
- [x] Created implementation plan
- [x] Got user approval
- [x] Updated MidtransService.php with Core API implementation
- [x] Added new `createCoreApiQrisPayment()` method to MidtransService
- [x] Implemented proper Core API request structure
- [x] Set 10-minute expiry time
- [x] Handle QR string and actions response
- [x] Updated PaymentController to use new method

### 🔄 In Progress
- [ ] Test the new implementation

### 📋 TODO
- [ ] Test with sample order
- [ ] Verify QR code generation
- [ ] Confirm expiry time works

## Implementation Details

### API Changes
- **From:** Snap API (`/snap/v1/transactions`)
- **To:** Core API (`/v2/charge`)
- **Payment Type:** `qris`
- **Acquirer:** `gopay`
- **Expiry:** 10 minutes (was 1 hour)

### Response Structure
```json
{
    "status_code": "201",
    "status_message": "Qris transaction is created",
    "transaction_id": "6ae757a4-e170-473a-94dd-d04171896860",
    "order_id": "order-{{timestamp}}",
    "qr_string": "00020101021226610014COM.GO-JEK.WWW...",
    "actions": [
        {
            "name": "generate-qr-code",
            "method": "GET",
            "url": "https://api.midtrans.com/v2/qris/6ae757a4-e170-473a-94dd-d04171896860/qr-code"
        }
    ],
    "expiry_time": "2025-08-22 16:12:00"
}
