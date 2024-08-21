export const TASK_LIST_VALUES = {
    CREATE_POST_TYPE: 'createPostType',
    CREATE_CONTENT_TYPE: 'createContentType',
    CREATE_TAXONOMY: 'createTaxonomy',
    CREATE_VENDOR: 'createVendor',
    UPDATE_VENDOR: 'updateVendor',
    DELETE_VENDOR: 'deleteVendor',
    WITHDRAW_REQUEST: 'withdrawRequest',
    REFUND_REQUEST: 'refundRequest',
}

export const TASK_LIST = [
    { label: 'Add New Post Type', value: TASK_LIST_VALUES.CREATE_POST_TYPE },
    { label: 'Add New Custom Content Type', value: TASK_LIST_VALUES.CREATE_CONTENT_TYPE },
    { label: 'Add New Taxonomy', value: TASK_LIST_VALUES.CREATE_TAXONOMY },
]