export const TASK_LIST_VALUES = {
    CREATE_POST_TYPE: 'createPostType',
    CREATE_CONTENT_TYPE: 'createContentType',
    CREATE_TAXONOMY: 'createTaxonomy',
    CREATE_RELATION: 'createRelation',
    UPDATE_POST_TYPE: 'updatePostType',
    UPDATE_CONTENT_TYPE: 'updateContentType',
    UPDATE_TAXONOMY: 'updateTaxonomy',
    UPDATE_RELATION: 'updateRelation',
}

export const TASK_LIST = [
    { label: 'Add New Post Type', value: TASK_LIST_VALUES.CREATE_POST_TYPE },
    { label: 'Add New Custom Content Type', value: TASK_LIST_VALUES.CREATE_CONTENT_TYPE },
    { label: 'Add New Taxonomy', value: TASK_LIST_VALUES.CREATE_TAXONOMY },
    { label: 'Add New Relation', value: TASK_LIST_VALUES.CREATE_RELATION },
    { label: 'Update Post Type', value: TASK_LIST_VALUES.UPDATE_POST_TYPE },
    { label: 'Update Custom Content Type', value: TASK_LIST_VALUES.UPDATE_CONTENT_TYPE },
    { label: 'Update Taxonomy', value: TASK_LIST_VALUES.UPDATE_TAXONOMY },
    { label: 'Update Relation', value: TASK_LIST_VALUES.UPDATE_RELATION },
]