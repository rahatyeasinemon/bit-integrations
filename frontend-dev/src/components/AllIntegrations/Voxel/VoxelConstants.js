import { __ } from '../../../Utils/i18nwrap'

export const TASK_LIST_VALUES = {
  NEW_POST: 'newPost',
  NEW_COLLECTION_POST: 'newCollectionPost',
  NEW_PROFILE: 'newProfile',
  UPDATE_POST: 'updatePost',
  UPDATE_COLLECTION_POST: 'updateCollectionPost',
  UPDATE_PROFILE: 'updateProfile',
  SET_POST_VERIFIED: 'setPostVerified',
  SET_COLLECTION_POST_VERIFIED: 'setCollectionPostVerified',
}

export const TASK_LIST = [
  { label: __('Create New Post', 'bit-integrations'), value: TASK_LIST_VALUES.NEW_POST },
  { label: __('Create New Collection Post', 'bit-integrations'), value: TASK_LIST_VALUES.NEW_COLLECTION_POST },
  { label: __('Create New Profile', 'bit-integrations'), value: TASK_LIST_VALUES.NEW_PROFILE },
  { label: __('Update Post', 'bit-integrations'), value: TASK_LIST_VALUES.UPDATE_POST },
  { label: __('Update Collection Post', 'bit-integrations'), value: TASK_LIST_VALUES.UPDATE_COLLECTION_POST },
  { label: __('Update Profile', 'bit-integrations'), value: TASK_LIST_VALUES.UPDATE_PROFILE },
  { label: __('Set Post as Verified', 'bit-integrations'), value: TASK_LIST_VALUES.SET_POST_VERIFIED },
  { label: __('Set Collection Post as Verified', 'bit-integrations'), value: TASK_LIST_VALUES.SET_COLLECTION_POST_VERIFIED },
]

export const POST_TYPE_TASK_ARRAY = [
  TASK_LIST_VALUES.NEW_POST,
  TASK_LIST_VALUES.NEW_COLLECTION_POST,
  TASK_LIST_VALUES.UPDATE_POST,
  TASK_LIST_VALUES.UPDATE_COLLECTION_POST
]

// constants for static post type

export const COLLECTION_POST_TYPE = 'collection'
export const PROFILE_POST_TYPE = 'profile';