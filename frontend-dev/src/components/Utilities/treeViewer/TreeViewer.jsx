import { memo, useMemo, useState } from "react";
import TreeView, { flattenTree } from "react-accessible-treeview";
import { FaSquare, FaCheckSquare, FaMinusSquare } from "react-icons/fa";
import { IoMdAddCircleOutline, IoMdRemoveCircleOutline } from "react-icons/io";
import cx from "classnames";
import "./style.css";

function TreeViewer({ data = [], onChange }) {
    // const formatedData = useMemo(() => flattenTree(processData(data)), [data])
    const formatedData = dummyData
    console.log(JSON.stringify(formatedData))

    // const [selectedIds, setSelectedIds] = useState([]);

    // const onKeyDown = (e) => {
    //     if (e.key === "Enter") {
    //         getAndSetIds();
    //     }
    // };

    // const getAndSetIds = () => {
    //     setSelectedIds(
    //         document
    //             .querySelector("#txtIdsToSelect")
    //             .value.split(",")
    //             .filter(val => !!val.trim())
    //             .map((x) => {
    //                 if (isNaN(parseInt(x.trim()))) {
    //                     return x;
    //                 }
    //                 return parseInt(x.trim());
    //             })
    //     );
    // };

    const setChange = ({ element, isSelected }) => {
        const connector = findAddress(formatedData, element, '')
        console.log(isSelected)
        if (isSelected) {
            onChange(connector)
        } else {
            onChange(connector, true)
        }
    }

    return (
        <div className="bg-white max-h-96 rounded border py-3 table-webhook-div">
            <TreeView
                data={formatedData}
                aria-label="Checkbox tree"
                multiSelect
                // selectedIds={selectedIds}
                defaultExpandedIds={[1]}
                propagateSelect
                propagateSelectUpwards
                togglableSelect
                // onSelect={(props) => console.log('onSelect callback: ', props)}
                onNodeSelect={setChange}
                nodeRenderer={({
                    element,
                    isBranch,
                    isExpanded,
                    isSelected,
                    isHalfSelected,
                    isDisabled,
                    getNodeProps,
                    level,
                    handleSelect,
                    handleExpand,
                }) => {
                    return (
                        <div
                            {...getNodeProps({ onClick: handleExpand })}
                            style={{
                                marginLeft: 40 * (level - 1),
                                opacity: isDisabled ? 0.5 : 1,
                            }}
                        >
                            {isBranch && <ArrowIcon isOpen={isExpanded} />}
                            <CheckBoxIcon
                                className="checkbox-icon"
                                onClick={(e) => {
                                    handleSelect(e);
                                    e.stopPropagation();
                                }}
                                variant={
                                    isHalfSelected ? "some" : isSelected ? "all" : "none"
                                }
                            />
                            <span className="name">
                                {element.name}
                            </span>
                        </div>
                    );
                }}
            />
        </div>
    );
}

export default memo(TreeViewer)

const processData = (data, index = "") => {
    // Handle non-object (including null) and non-array data
    if (typeof data !== "object" || data === null) {
        return { name: index + ' (' + data + ')', value: data };
    }

    // Handle array data
    if (Array.isArray(data)) {
        return {
            name: index + ' (array)',
            value: 'Array',
            children: data.map((item, key) => processData(item, key)),
        };
    }

    // Handle object data
    return {
        name: index + ' (object)',
        value: 'Object',
        children: Object.keys(data).map((key) => processData(data[key], key)),
    };
}

const ArrowIcon = ({ isOpen, className }) => {
    const baseClass = "arrow";
    const classes = cx(
        baseClass,
        { [`${baseClass}--closed`]: !isOpen },
        { [`${baseClass}--open`]: isOpen },
        className
    )
    return isOpen ? <IoMdRemoveCircleOutline className={classes} /> : <IoMdAddCircleOutline className={classes} />
}

const CheckBoxIcon = ({ variant, ...rest }) => {
    switch (variant) {
        case "all":
            return <FaCheckSquare {...rest} />
        case "none":
            return <FaSquare {...rest} />
        case "some":
            return <FaMinusSquare {...rest} />
        default:
            return null
    }
};

const findAddress = (orgData, element, connector) => {
    const { id, name, parent } = element
    connector = name.substring(0, name.indexOf(' (')) + (connector ? '.' + connector : '')

    if (parent) {
        const parantElement = orgData.find(data => data?.id === parent)
        connector = findAddress(orgData, parantElement, connector)
    }

    return connector
}

const dummyData = flattenTree(processData([
    {
        "entry_id": 791,
        "entry_type": "custom-forms",
        "form_id": "7",
        "draft_id": null,
        "is_spam": false,
        "date_created_sql": null,
        "date_created": null,
        "time_created": null,
        "meta_data": []
    },
    "7",
    [
        {
            "name": "name-1",
            "value": {
                "first-name": "Ignatius",
                "last-name": "Perry"
            },
            "field_type": "name",
            "key": 0,
            "field_array": {
                "element_id": "name-1",
                "type": "name",
                "cols": "12",
                "required": "true",
                "field_label": "First Name",
                "placeholder": "E.g. John",
                "prefix_label": "Prefix",
                "fname_label": "First Name",
                "fname_placeholder": "E.g. John",
                "mname_label": "Middle Name",
                "mname_placeholder": "E.g. Smith",
                "lname_label": "Last Name",
                "lname_placeholder": "E.g. Doe",
                "wrapper_id": "wrapper-1511347711918-1669",
                "multiple_name": "true",
                "fname": "1",
                "lname": "1",
                "conditions": []
            },
            "form_field_obj": {
                "field": [],
                "form_settings": [],
                "name": "Name",
                "slug": "name",
                "category": "standard",
                "settings": [],
                "autofill_settings": {
                    "name": {
                        "values": {
                            "wp_user": {
                                "name": "WordPress User",
                                "attributes": {
                                    "wp_user.display_name": {
                                        "name": "Display Name"
                                    },
                                    "wp_user.login": {
                                        "name": "Username"
                                    },
                                    "wp_user.first_name": {
                                        "name": "First Name"
                                    },
                                    "wp_user.last_name": {
                                        "name": "Last Name"
                                    }
                                }
                            }
                        }
                    },
                    "name-prefix": {
                        "values": []
                    },
                    "name-first-name": {
                        "values": {
                            "wp_user": {
                                "name": "WordPress User",
                                "attributes": {
                                    "wp_user.first_name": {
                                        "name": "First Name"
                                    },
                                    "wp_user.display_name": {
                                        "name": "Display Name"
                                    },
                                    "wp_user.login": {
                                        "name": "Username"
                                    }
                                }
                            }
                        }
                    },
                    "name-middle-name": {
                        "values": []
                    },
                    "name-last-name": {
                        "values": {
                            "wp_user": {
                                "name": "WordPress User",
                                "attributes": {
                                    "wp_user.last_name": {
                                        "name": "Last Name"
                                    },
                                    "wp_user.login": {
                                        "name": "Username"
                                    }
                                }
                            }
                        }
                    }
                },
                "defaults": {
                    "field_label": "Name",
                    "placeholder": "E.g. John Doe",
                    "prefix_label": "Prefix",
                    "fname_label": "First Name",
                    "fname_placeholder": "E.g. John",
                    "mname_label": "Middle Name",
                    "mname_placeholder": "E.g. Smith",
                    "lname_label": "Last Name",
                    "lname_placeholder": "E.g. Doe",
                    "prefix": "true",
                    "fname": "true",
                    "mname": "true",
                    "lname": "true",
                    "required_message": "Name is required.",
                    "prefix_required_message": "Prefix is required.",
                    "fname_required_message": "First Name is required.",
                    "mname_required_message": "Middle Name is required.",
                    "lname_required_message": "Last Name is required.",
                    "layout_columns": "2"
                },
                "hide_advanced": false,
                "position": 1,
                "is_input": false,
                "has_counter": false,
                "is_valid": true,
                "validation_message": [],
                "icon": "sui-icon-profile-male",
                "is_calculable": false,
                "type": "name",
                "options": []
            }
        },
        {
            "name": "email-1",
            "value": "bisep@mailinator.com",
            "field_type": "email",
            "key": 1,
            "field_array": {
                "element_id": "email-1",
                "type": "email",
                "cols": "12",
                "required": "true",
                "field_label": "Email Address",
                "placeholder": "E.g. john@doe.com",
                "validation": "1",
                "validation_text": "",
                "wrapper_id": "wrapper-1511347712118-1739",
                "conditions": []
            },
            "form_field_obj": {
                "field": [],
                "form_settings": [],
                "name": "Email",
                "slug": "email",
                "category": "standard",
                "settings": [],
                "autofill_settings": {
                    "email": {
                        "values": {
                            "wp_user": {
                                "name": "WordPress User",
                                "attributes": {
                                    "wp_user.email": {
                                        "name": "Email"
                                    }
                                }
                            }
                        }
                    }
                },
                "defaults": {
                    "validation": false,
                    "placeholder": "E.g. john@doe.com",
                    "field_label": "Email Address"
                },
                "hide_advanced": false,
                "position": 2,
                "is_input": true,
                "has_counter": false,
                "is_valid": true,
                "validation_message": [],
                "icon": "sui-icon-mail",
                "is_calculable": false,
                "type": "email",
                "options": []
            }
        },
        {
            "name": "phone-1",
            "value": "+1 (342) 693-2793",
            "field_type": "phone",
            "key": 2,
            "field_array": {
                "element_id": "phone-1",
                "type": "phone",
                "cols": "12",
                "required": "",
                "field_label": "Phone Number",
                "placeholder": "E.g. +1 3004005000",
                "validation": "none",
                "phone_validation_type": "standard",
                "validation_text": "",
                "wrapper_id": "wrapper-1311247712118-1194",
                "conditions": []
            },
            "form_field_obj": {
                "field": [],
                "form_settings": [],
                "name": "Phone",
                "slug": "phone",
                "category": "standard",
                "settings": [],
                "autofill_settings": {
                    "phone": {
                        "values": []
                    }
                },
                "defaults": {
                    "required": false,
                    "limit": 10,
                    "limit_type": "characters",
                    "validation": "none",
                    "field_label": "Phone",
                    "placeholder": "E.g. +1 300 400 5000"
                },
                "hide_advanced": false,
                "position": 3,
                "is_input": true,
                "has_counter": true,
                "is_valid": true,
                "validation_message": [],
                "icon": "sui-icon-phone",
                "is_calculable": false,
                "type": "phone",
                "options": []
            }
        },
        {
            "name": "text-1",
            "value": "Parrish and Galloway Traders",
            "field_type": "text",
            "key": 3,
            "field_array": {
                "element_id": "text-1",
                "type": "text",
                "options": [],
                "cols": "12",
                "conditions": [],
                "wrapper_id": "wrapper-4387-9835",
                "input_type": "line",
                "limit_type": "characters",
                "field_label": "Company",
                "placeholder": "E.g. text placeholder"
            },
            "form_field_obj": {
                "field": [],
                "form_settings": [],
                "name": "Input",
                "slug": "text",
                "category": "standard",
                "settings": [],
                "autofill_settings": {
                    "text": {
                        "values": {
                            "wp_user": {
                                "name": "WordPress User",
                                "attributes": {
                                    "wp_user.display_name": {
                                        "name": "Display Name"
                                    },
                                    "wp_user.first_name": {
                                        "name": "First Name"
                                    },
                                    "wp_user.last_name": {
                                        "name": "Last Name"
                                    },
                                    "wp_user.login": {
                                        "name": "Username"
                                    },
                                    "wp_user.description": {
                                        "name": "Description"
                                    }
                                }
                            }
                        }
                    }
                },
                "defaults": {
                    "input_type": "line",
                    "limit_type": "characters",
                    "field_label": "Text",
                    "placeholder": "E.g. text placeholder"
                },
                "hide_advanced": false,
                "position": 6,
                "is_input": true,
                "has_counter": true,
                "is_valid": true,
                "validation_message": [],
                "icon": "sui-icon-style-type",
                "is_calculable": false,
                "type": "text",
                "options": []
            }
        },
        {
            "name": "_forminator_user_ip",
            "value": "127.0.0.1"
        }
    ]
]))