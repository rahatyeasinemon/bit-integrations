export const TASK_LIST_VALUES = {
    CREATE_VENDOR: 'createVendor',
    UPDATE_VENDOR: 'updateVendor',
    DELETE_VENDOR: 'deleteVendor',
    WITHDRAW_REQUEST: 'withdrawRequest',
    REFUND_REQUEST: 'refundRequest',
}

export const TASK_LIST = [
    { label: 'Create A New Vendor', value: TASK_LIST_VALUES.CREATE_VENDOR },
    { label: 'Update A Vendor', value: TASK_LIST_VALUES.UPDATE_VENDOR },
    { label: 'Delete A Vendor', value: TASK_LIST_VALUES.DELETE_VENDOR },
    { label: 'Request A Withdraw', value: TASK_LIST_VALUES.WITHDRAW_REQUEST },
    { label: 'Request A Refund (Dokan pro feature)', value: TASK_LIST_VALUES.REFUND_REQUEST },
]