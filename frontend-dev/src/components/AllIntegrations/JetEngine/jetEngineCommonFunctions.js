/* eslint-disable no-console */
/* eslint-disable no-else-return */
import toast from 'react-hot-toast'
import bitsFetch from '../../../Utils/bitsFetch'
import { __ } from '../../../Utils/i18nwrap'
import { create } from 'mutative'
import { TASK_LIST_VALUES } from './jetEngineConstants'

export const handleInput = (e, jetEngineConf, setJetEngineConf) => {
  const newConf = create(jetEngineConf, draftConf => {
    const { name } = e.target
    if (e.target.value !== '') {
      draftConf[name] = e.target.value
    } else {
      delete draftConf[name]
    }
  })

  setJetEngineConf(newConf)
}

export const checkMappedFields = (jetEngineConf) => {
  const mappedFields = jetEngineConf?.field_map ? jetEngineConf.field_map.filter(mappedField => (!mappedField.formField || !mappedField.jetEngineField || (!mappedField.formField === 'custom' && !mappedField.customValue))) : []
  if (mappedFields.length > 0) {
    return false
  }
  return true
}

export const jetEngineAuthentication = (confTmp, setError, setIsAuthorized, loading, setLoading) => {
  if (!confTmp.name) {
    setError({ name: !confTmp.name ? __('Name can\'t be empty', 'bit-integrations') : '' })
    return
  }

  setLoading({ ...loading, auth: true })
  bitsFetch({}, 'jetEngine_authentication')
    .then(result => {
      if (result.success) {
        setIsAuthorized(true)
        toast.success(__('Connected Successfully', 'bit-integrations'))
        setLoading({ ...loading, auth: false })
        return
      }
      setLoading({ ...loading, auth: false })
      toast.error(__('Connection failed: install and active JetEngine plugin first!', 'bit-integrations'))
    })
}

export const getJetEngineOptions = (route, actionOptions, setActionsOptions, type, loading, setLoading) => {
  if (!route) {
    return
  }

  setLoading({ ...loading, cptOptions: true })

  bitsFetch({}, route)
    .then(result => {
      if (result.success && result.data) {
        const tmpOptions = { ...actionOptions }
        tmpOptions[type] = result.data
        setActionsOptions(tmpOptions)
        setLoading({ ...loading, cptOptions: false })
        toast.success(__('Menu Positions fetched successfully', 'bit-integrations'))
        return
      }
      setLoading({ ...loading, cptOptions: false })
      toast.error(__(result?.data ? result.data : 'Something went wrong!', 'bit-integrations'))
    })

}

export const getJetEngineRelationTypes = (confTmp, setConf, loading, setLoading) => {
  setLoading({ ...loading, relation_types: true })

  bitsFetch({}, 'jetEngine_relation_types')
    .then(result => {
      if (result.success && result.data) {
        const newConf = { ...confTmp }
        newConf.allRelationTypes = result.data
        setConf(newConf)
        setLoading({ ...loading, relation_types: false })
        toast.success(__('Realtion object fetched successfully', 'bit-integrations'))
        return
      }
      setLoading({ ...loading, relation_types: false })
      toast.error(__(result?.data ? result.data : 'Something went wrong!', 'bit-integrations'))
    })

}

export const getJetEngineCPTList = (confTmp, setConf, loading, setLoading) => {
  setLoading({ ...loading, cptList: true })

  bitsFetch({}, 'jetEngine_cpt_list')
    .then(result => {
      if (result.success && result.data) {
        const newConf = { ...confTmp }
        newConf.cptList = result.data
        setConf(newConf)
        setLoading({ ...loading, cptList: false })
        toast.success(__('CPT list fetched successfully', 'bit-integrations'))
        return
      }
      setLoading({ ...loading, cptList: false })
      toast.error(__(result?.data ? result.data : 'Something went wrong!', 'bit-integrations'))
    })
}

export const getJetEngineCCTList = (confTmp, setConf, loading, setLoading) => {
  setLoading({ ...loading, cctList: true })

  bitsFetch({}, 'jetEngine_cct_list')
    .then(result => {
      if (result.success && result.data) {
        const newConf = { ...confTmp }
        newConf.cctList = result.data
        setConf(newConf)
        setLoading({ ...loading, cctList: false })
        toast.success(__('CCT list fetched successfully', 'bit-integrations'))
        return
      }
      setLoading({ ...loading, cctList: false })
      toast.error(__(result?.data ? result.data : 'Something went wrong!', 'bit-integrations'))
    })
}

export const getJetEngineTaxList = (confTmp, setConf, loading, setLoading) => {
  setLoading({ ...loading, taxList: true })

  bitsFetch({}, 'jetEngine_tax_list')
    .then(result => {
      if (result.success && result.data) {
        const newConf = { ...confTmp }
        newConf.taxList = result.data
        setConf(newConf)
        setLoading({ ...loading, taxList: false })
        toast.success(__('Taxonomy list fetched successfully', 'bit-integrations'))
        return
      }
      setLoading({ ...loading, taxList: false })
      toast.error(__(result?.data ? result.data : 'Something went wrong!', 'bit-integrations'))
    })
}

export const getJetEngineRelationList = (confTmp, setConf, loading, setLoading) => {
  setLoading({ ...loading, relationList: true })

  bitsFetch({}, 'jetEngine_relation_list')
    .then(result => {
      if (result.success && result.data) {
        const newConf = { ...confTmp }
        newConf.relationList = result.data
        setConf(newConf)
        setLoading({ ...loading, relationList: false })
        toast.success(__('Taxonomy list fetched successfully', 'bit-integrations'))
        getJetEngineRelationTypes(newConf, setConf, loading, setLoading)
        return
      }
      setLoading({ ...loading, relationList: false })
      toast.error(__(result?.data ? result.data : 'Something went wrong!', 'bit-integrations'))
    })
}

export const jetEngineStaticFields = (selectedTask) => {
  if (selectedTask === TASK_LIST_VALUES.CREATE_POST_TYPE || selectedTask === TASK_LIST_VALUES.UPDATE_POST_TYPE) {
    return {
      staticFields: [
        { key: 'name', label: 'Post Type Name', required: selectedTask === TASK_LIST_VALUES.CREATE_POST_TYPE ? true : false },
        { key: 'singular_name', label: 'Singular Name', required: false },
        { key: 'add_new', label: 'Add New', required: false },
        { key: 'add_new_item', label: 'Add New Item', required: false },
        { key: 'new_item', label: 'New Item', required: false },
        { key: 'edit_item', label: 'Edit Item', required: false },
        { key: 'view_item', label: 'View Item', required: false },
        { key: 'all_items', label: 'All Items', required: false },
        { key: 'search_items', label: 'Search for items', required: false },
        { key: 'parent_item_colon', label: 'Parent Item', required: false },
        { key: 'not_found', label: 'Not Found', required: false },
        { key: 'not_found_in_trash', label: 'Not Found In trash', required: false },
        { key: 'menu_name', label: 'Admin Menu', required: false },
        { key: 'name_admin_bar', label: 'Add New on Toolbar', required: false },
        { key: 'featured_image', label: 'Featured Image', required: false },
        { key: 'set_featured_image', label: 'Set Featured Image', required: false },
        { key: 'remove_featured_image', label: 'Remove Featured Image', required: false },
        { key: 'use_featured_image', label: 'Use Featured Image', required: false },
        { key: 'archives', label: 'The post type archive label used in nav menus', required: false },
        { key: 'insert_into_item', label: 'Insert into post', required: false },
        { key: 'uploaded_to_this_item', label: 'Uploaded to this post', required: false },
      ],
      fieldMap: selectedTask === TASK_LIST_VALUES.CREATE_POST_TYPE ? [{ formField: '', jetEngineField: 'name' }] : [{ formField: '', jetEngineField: '' }]
    }
  } else if (selectedTask === TASK_LIST_VALUES.CREATE_CONTENT_TYPE || selectedTask === TASK_LIST_VALUES.UPDATE_CONTENT_TYPE) {
    return {
      staticFields: [
        { key: 'name', label: 'Content Type Name', required: selectedTask === TASK_LIST_VALUES.CREATE_CONTENT_TYPE ? true : false },
        { key: 'capability', label: 'Content Type UI Access Capability', required: false },
      ],
      fieldMap: selectedTask === TASK_LIST_VALUES.CREATE_CONTENT_TYPE ?
        [{ formField: '', jetEngineField: 'name' }] : [{ formField: '', jetEngineField: '' }]
    }
  } else if (selectedTask === TASK_LIST_VALUES.CREATE_TAXONOMY || selectedTask === TASK_LIST_VALUES.UPDATE_TAXONOMY) {
    return {
      staticFields: [
        { key: 'name', label: 'Taxonomy Name', required: selectedTask === TASK_LIST_VALUES.CREATE_TAXONOMY ? true : false },
        { key: 'singular_name', label: 'Singular name', required: false },
        { key: 'menu_name', label: 'Menu name text', required: false },
        { key: 'all_items', label: 'All items text', required: false },
        { key: 'edit_item', label: 'Edit item text', required: false },
        { key: 'view_item', label: 'View Item', required: false },
        { key: 'update_item', label: 'Update item text', required: false },
        { key: 'add_new_item', label: 'Add new item text', required: false },
        { key: 'new_item_name', label: 'New item name text', required: false },
        { key: 'parent_item', label: 'Parent item text', required: false },
        { key: 'parent_item_colon', label: 'Parent item with colon', required: false },
        { key: 'search_items', label: 'Search items text', required: false },
        { key: 'popular_items', label: 'Popular items text', required: false },
        { key: 'separate_items_with_commas', label: 'Separate item with commas text', required: false },
        { key: 'add_or_remove_items', label: 'Add or remove items text', required: false },
        { key: 'choose_from_most_used', label: 'Choose from most used text', required: false },
        { key: 'not_found', label: 'Items not found text', required: false },
        { key: 'back_to_items', label: 'Back to items text', required: false },
        { key: 'query_var', label: 'Register Query Var', required: false },
        { key: 'capability_type', label: 'Capability Type', required: false },
        { key: 'description', label: 'Taxonomy Description', required: false },
      ],
      fieldMap: selectedTask === TASK_LIST_VALUES.CREATE_TAXONOMY ? [{ formField: '', jetEngineField: 'name' }] : [{ formField: '', jetEngineField: '' }]
    }
  } else if (selectedTask === TASK_LIST_VALUES.CREATE_RELATION || selectedTask === TASK_LIST_VALUES.UPDATE_RELATION) {
    return {
      staticFields: [
        { key: 'name', label: 'Relation Name', required: selectedTask === TASK_LIST_VALUES.CREATE_RELATION ? true : false },
        { key: 'parent_page_control_title', label: 'Parent Object: label of relation box', required: false },
        { key: 'parent_page_control_connect', label: 'Parent Object: label of connect button', required: false },
        { key: 'parent_page_control_select', label: 'Parent Object: label of select item control', required: false },
        { key: 'child_page_control_title', label: 'Child Object: label of relation box', required: false },
        { key: 'child_page_control_connect', label: 'Child Object: label of connect button', required: false },
        { key: 'child_page_control_select', label: 'Child Object: label of select item control', required: false },
      ],
      fieldMap: selectedTask === TASK_LIST_VALUES.CREATE_RELATION ? [{ formField: '', jetEngineField: 'name' }] : [{ formField: '', jetEngineField: '' }]
    }
  }

  return { staticFields: [], fieldMap: [] }
}

