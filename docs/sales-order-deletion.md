# Sales Order deletion

Users with `sales_order_delete` can delete an SO from the Sales Order list at any stage. SO deletion also deletes every dispatch/return and its item rows. Payment history remains for refunds.

The operation locks the SO and runs in one database transaction. Stock is restored to each original warehouse/product using the net inventory movement for that SO: dispatched quantity minus quantities already received through accepted returns. Pending return requests do not restore stock. Original movements remain in the inventory ledger, and compensating `sales_delete_reversal` entries show the new balance. Repeated deletion cannot restore stock twice.

The SO is marked cancelled and soft-deleted. SO item and payment history remains for audit; dispatch/return rows are removed, and inventory movements remain in the ledger. Deleted orders disappear from active Sales and Dispatch queues, and their badge counts refresh. Further payment, confirmation, dispatch and return acceptance cannot operate on deleted orders.

Deleted SOs with payments greater than zero remain visible in Payment Ledger as **Deleted · Refund Pending**. Their collectible balance is zero. Payment Details displays the total received as refund pending and retains all receipt history. Refunds must be arranged separately; this change does not send money or provide a refund-settlement workflow. No database migration is required.

## Individual dispatch deletion

In Dispatch Desk, open **Dispatch History** and choose **Delete Dispatch** for a specific DSP record. Select Fully Dispatched or All to find completed SOs. The action requires `dispatch_access` and `sales_order_delete`.

Only the selected dispatch and its item rows are deleted. Its quantity returns to the original warehouse, and dispatched quantities on the SO items decrease accordingly. Payment records and order totals do not change, and no refund is raised.

If no quantities remain dispatched, the SO becomes `confirmed` (Ready to Dispatch). If any quantity remains dispatched and some quantity is pending, it becomes `partially_dispatched`. Pending quantities and the Dispatch Desk badge refresh. A compensating `dispatch_delete_reversal` entry retains the stock audit trail; a later SO deletion reverses only the remaining net stock.

Individual dispatch deletion is blocked if a return request exists on the SO, because returns are linked to the SO rather than a particular DSP. Whole-SO deletion still handles accepted returns and net stock reversal.
