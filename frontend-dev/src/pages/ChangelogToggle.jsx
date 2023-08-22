
import { Fragment, useState } from 'react'
import { useFela } from 'react-fela'
import ChangelogIcn from '../Icons/ChangeLogIcn'
import ExternalLinkIcn from '../Icons/ExternalLinkIcn'
import bitsFetch from '../Utils/bitsFetch'
import { __ } from '../Utils/i18nwrap'
import changelogInfo from '../Utils/StaticData/changeloginfo'
import { $btcbi } from '../GlobalStates'
import Modal from '../components/Utilities/Modal'
import { useRecoilValue } from 'recoil'

export default function ChangelogToggle() {
    const btcbi = useRecoilValue($btcbi)
    const [show, setShow] = useState(btcbi.changelogVersion !== btcbi.version)
    const [currentVersion, setCurrentVersion] = useState(btcbi.version)
    const currenChangelog = changelogInfo['2.3.0']
    const { css } = useFela()

    // const setChangeLogVersion = () => {
    //     setShow(false)
    //     bitsFetch({
    //         version: btcbi.version,
    //     }, 'bitforms_changelog_version')
    //         .then(() => {
    //             console.log('done')
    //             setBits(prevBits => ({ ...prevBits, changelogVersion: prevBits.version }))
    //         })
    // }

    if (!currenChangelog) return

    return (
        <div className="changelog-toggle">
            <button
                title={__('What\'s New')}
                type="button"
                className={css(styles.button)}
                onClick={() => setShow(true)}
            >
                {/* <QuestionIcn size={25} /> */}
                <ChangelogIcn size={25} />
            </button>
            <Modal sm show={show} onCloseMdl={setChangeLogVersion}>
                <div>
                    <div className="flx flx-col flx-center">
                        <h3 className={css({ m: 5 })}>{__('What\'s New?')}</h3>
                    </div>
                    <div>
                        <h3 className={css({ m: 0 })}>
                            <a href="https://bitapps.pro/docs/bit-form/changelog/" target="_blank" rel="noreferrer">
                                {'Version 2.3.0 '}
                                <ExternalLinkIcn size="14" />
                            </a>
                        </h3>
                        <p className={css({ m: '0px 5px 5px' })}>{`Date: ${currenChangelog.date}`}</p>
                    </div>
                    <div className={css(styles.content)}>
                        {Object.entries(currenChangelog.changes).map(([titile, obj]) => (
                            <div className={css({ p: '0px 5px' })} key={titile}>
                                <span className={css(styles.bdg, styles[titile])}>{obj.label}</span>
                                {obj.tag && <span className={css(styles.tag)}>{obj.tag}</span>}
                                <ul className={css(styles.ul, { mt: 5 })}>
                                    {obj.list.map((tempObj, index) => getChangesList(tempObj, css, `${titile}-${index}`))}
                                </ul>
                            </div>
                        ))}
                    </div>
                    <div className={css({})}>
                        <span className={css({ m: '0px 5px 5px' })}>{__('For more details,')}</span>
                        <a href="https://bitapps.pro/docs/bit-form/changelog/" target="_blank" rel="noreferrer">
                            {__('Click here ')}
                            <ExternalLinkIcn size="14" />
                        </a>
                    </div>
                </div>
            </Modal>
        </div>
    )
}

function getChangesList(listObj, css, parentKey = '') {
    if (typeof listObj === 'string') return <li key={parentKey}>{listObj}</li>
    if (Array.isArray(listObj)) return listObj.map((tempObj, index) => getChangesList(tempObj, css, `${parentKey}-${index}`))
    if (typeof listObj === 'object') {
        const { label, tag, list } = listObj
        return (
            <Fragment key={parentKey}>
                <li>
                    {label}
                    {tag && <span className={css(styles.tag)}>{tag}</span>}
                </li>
                {
                    list && (
                        <ul className={css(styles.ul)}>
                            {list.map((tempObj, index) => getChangesList(tempObj, css, `${parentKey}-${index}`))}
                        </ul>
                    )
                }

            </Fragment>
        )
    }
}

const styles = {
    content: {
        owy: 'scroll',
        mxh: '70vh',
    },
    button: {
        b: 'none',
        cr: 'white',
        brs: '10px',
        curp: 'pointer',
        flx: 'center',
        h: '40px',
        w: '40px',
        bd: '#70707085',
        mr: '10px',
        '&:hover': {
            bd: '#707070',
        },
    },
    ul: {
        ml: 20,
        '> li': {
            mb: 10,
            listStyle: 'circle',
            fs: 14,
            fw: 400,
        },
    },
    tag: {
        bd: '#f6cbff',
        brs: '20px',
        cr: '#161a2e !important',
        fs: '.75rem',
        fw: '500',
        lh: '1.4',
        p: '2px 13px',
        ml: '5px',
    },
    bdg: {
        brs: '5px',
        dy: 'inline-block',
        fw: '500',
        lh: '1.4',
        p: '2px 13px',
        pn: 'relative',
        '&::before': {
            bd: 'inherit',
            brs: '3px',
            ct: '',
            h: '20px',
            lt: '-6px',
            pn: 'absolute',
            tp: '3px',
            tm: 'rotate(45deg)',
            w: '20px',
            zx: '-1',
        },
    },
    added: {
        bd: '#0DB1FF',
        c: '#24292e',
    },
    imporovement: {
        bd: '#00FFBF',
        c: '#24292e',
    },
    fixed: {
        bd: '#FFD000',
        c: '#24292e',
    },
}
