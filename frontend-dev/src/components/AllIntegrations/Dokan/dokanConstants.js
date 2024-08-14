export const TASK_LIST_VALUES = {
    CREATE_VENDOR: 'createVendor',
    UPDATE_VENDOR: 'updateVendor',
    DELETE_VENDOR: 'deleteVendor',
    WITHDRAW_REQUEST: 'withdrawRequest',
    USER_REPUTATION: 'userReputation',
    ADD_TO_GROUP: 'addToGroup',
    REMOVE_FROM_GROUP: 'removeFromGroup',
    CREATE_TOPIC: 'createTopic',
}

export const TASK_LIST = [
    { label: 'Create A New Vendor', value: TASK_LIST_VALUES.CREATE_VENDOR },
    { label: 'Update A Vendor', value: TASK_LIST_VALUES.UPDATE_VENDOR },
    { label: 'Delete A Vendor', value: TASK_LIST_VALUES.DELETE_VENDOR },
    { label: 'Request A Withdraw', value: TASK_LIST_VALUES.WITHDRAW_REQUEST },
]