import { __ } from '../../../Utils/i18nwrap'

export const TASK_LIST_VALUES = {
  CREATE_POST_TYPE: 'createPostType',
  CREATE_CONTENT_TYPE: 'createContentType',
  CREATE_TAXONOMY: 'createTaxonomy',
  CREATE_RELATION: 'createRelation'
}

export const TASK_LIST = [
  { label: __('Add New Post Type', 'bit-integrations'), value: TASK_LIST_VALUES.CREATE_POST_TYPE },
  {
    label: __('Add New Custom Content Type', 'bit-integrations'),
    value: TASK_LIST_VALUES.CREATE_CONTENT_TYPE
  },
  { label: __('Add New Taxonomy', 'bit-integrations'), value: TASK_LIST_VALUES.CREATE_TAXONOMY },
  { label: __('Add New Relation', 'bit-integrations'), value: TASK_LIST_VALUES.CREATE_RELATION }
]
