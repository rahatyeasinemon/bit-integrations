import { memo, useMemo, useState } from "react";
import TreeView, { flattenTree } from "react-accessible-treeview";
import { FaRegCheckSquare, FaRegSquare, FaRegMinusSquare } from "react-icons/fa";
import { IoMdAddCircleOutline, IoMdRemoveCircleOutline } from "react-icons/io";
import cx from "classnames";
import "./style.css";

function TreeViewer({ data = [], onChange }) {
    const formatedData = useMemo(() => flattenTree(processData(data)), [data])

    const setChange = ({ element, isSelected }) => {
        const connector = findAddress(formatedData, element, '')
        if (isSelected) {
            onChange(connector)
        } else {
            onChange(connector, true)
        }
    }

    return (
        <div className="bg-white rounded border py-3 table-webhook-div shadow" style={{ maxHeight: '600px' }}>
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
            return <FaRegCheckSquare {...rest} />
        case "none":
            return <FaRegSquare {...rest} />
        case "some":
            return <FaRegMinusSquare   {...rest} />
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
