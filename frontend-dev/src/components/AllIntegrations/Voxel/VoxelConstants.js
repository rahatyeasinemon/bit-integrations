import { __ } from '../../../Utils/i18nwrap'

export const TASK_LIST_VALUES = {
  NEW_POST: 'newPost',
  NEW_COLLECTION_POST: 'newCollectionPost',
  NEW_PROFILE: 'newProfile'
}

export const TASK_LIST = [
  { label: __('Create New Post', 'bit-integrations'), value: TASK_LIST_VALUES.NEW_POST },
  { label: __('Create New Collection Post', 'bit-integrations'), value: TASK_LIST_VALUES.NEW_COLLECTION_POST },
  { label: __('Create New Profile', 'bit-integrations'), value: TASK_LIST_VALUES.NEW_PROFILE },
]

export const POST_TYPE_TASK_ARRAY = [
  TASK_LIST_VALUES.NEW_POST,
  TASK_LIST_VALUES.NEW_COLLECTION_POST
]

// constants for static post type

export const COLLECTION_POST_TYPE = 'collection'
export const PROFILE_POST_TYPE = 'profile';