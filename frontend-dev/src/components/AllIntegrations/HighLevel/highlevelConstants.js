import { __ } from "../../../Utils/i18nwrap"

export const TASK_LIST_VALUES = {
    CREATE_CONTACT: 'createContact',
    UPDATE_CONTACT: 'updateContact',
    CREATE_TASK: 'createTask',
    UPDATE_TASK: 'updateTask'
}

export const TASK_LIST = [
    { label: __('Create Contact', 'bit-integrations'), value: TASK_LIST_VALUES.CREATE_CONTACT },
    { label: __('Update Contact', 'bit-integrations'), value: TASK_LIST_VALUES.UPDATE_CONTACT },
    { label: __('Create Task', 'bit-integrations'), value: TASK_LIST_VALUES.CREATE_TASK },
    { label: __('Update Task', 'bit-integrations'), value: TASK_LIST_VALUES.UPDATE_TASK },
]

export const OPTIONAL_FIELD_MAP_ARRAY = [TASK_LIST_VALUES.UPDATE_CONTACT]