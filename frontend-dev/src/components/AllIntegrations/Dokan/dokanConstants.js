export const TASK_LIST_VALUES = {
    CREATE_VENDOR: 'createVendor',
    UPDATE_VENDOR: 'updateVendor',
    DELETE_VENDOR: 'deleteVendor',
    USER_REPUTATION: 'userReputation',
    ADD_TO_GROUP: 'addToGroup',
    REMOVE_FROM_GROUP: 'removeFromGroup',
    CREATE_TOPIC: 'createTopic',
}

export const TASK_LIST = [
    { label: 'Create A New Vendor', value: TASK_LIST_VALUES.CREATE_VENDOR },
    { label: 'Update A Vendor', value: TASK_LIST_VALUES.UPDATE_VENDOR },
    { label: 'Delete A Vendor', value: TASK_LIST_VALUES.DELETE_VENDOR },
    { label: 'Set User Reputation', value: TASK_LIST_VALUES.USER_REPUTATION },
    { label: 'Add User to Group', value: TASK_LIST_VALUES.ADD_TO_GROUP },
    { label: 'Remove User from Group', value: TASK_LIST_VALUES.REMOVE_FROM_GROUP },
    { label: 'Create a New Topic', value: TASK_LIST_VALUES.CREATE_TOPIC },
]