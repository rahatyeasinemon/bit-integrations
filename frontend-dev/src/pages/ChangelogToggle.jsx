
import { Fragment, useState } from 'react'
import { useRecoilValue } from 'recoil'
import { $btcbi } from '../GlobalStates'
import ChangelogIcn from '../Icons/ChangeLogIcn'
import ExternalLinkIcn from '../Icons/ExternalLinkIcn'
import { __ } from '../Utils/i18nwrap'
import Modal from '../components/Utilities/Modal'

export default function ChangelogToggle() {
    const btcbi = useRecoilValue($btcbi)
    const [show, setShow] = useState(btcbi.changelogVersion !== btcbi.version)


    return (
        <div className="changelog-toggle">
            <button
                title={__('What\'s New')}
                type="button"
                className={styles.button}
                onClick={() => setShow(true)}
                style={{
                    borderRadius: '12px',
                    background: '#fff',
                    padding: '6px',
                }}
            >
                {/* <QuestionIcn size={25} /> */}
                <ChangelogIcn size={25} />
            </button>
            <Modal sm show={show} setModal={setShow} >
                <div style={{ padding: '0px 30px' }}>
                    <h4 style={{
                        backgroundColor: '#ff000012',
                        color: 'red',
                        padding: '4px 8px',
                        textAlign: 'center',
                        borderRadius: '5px',
                    }}> From next update,To use pro plugin free version is required. </h4>
                    <div className="flx flx-col flx-center">
                        <h3 style={{ margin: 5 }}>{__('What\'s New?')}</h3>
                        <small>22 August 2023</small>
                    </div>
                    <div style={{ marginTop: '10px' }}>
                        {/* <h4>New Integration</h4> */}
                        {/* <p>New Integration</p> */}
                        <span style={{
                            backgroundColor: 'green',
                            color: 'white',
                            padding: '4px 8px',
                            textAlign: 'center',
                            borderRadius: '5px',
                        }}><b>New Integration</b></span>

                        <div>
                            <ul style={{ listStyleType: 'number' }}>
                                <li>Perfex</li>
                                <li>OneHash</li>
                                <li>Salesflare</li>
                            </ul>
                        </div>

                        <span style={{
                            backgroundColor: 'yellow',
                            color: 'black',
                            padding: '4px 8px',
                            textAlign: 'center',
                            borderRadius: '5px',
                        }}><b>Fixed</b></span>

                        <div>
                            <ul style={{ listStyleType: 'circle' }}>
                                <li>ClickUp Log success message issue fixed</li>
                                <li>ClinchPad Log success message issue fixed</li>
                                <li>GiveWP Purchase Value Issue fixed</li>
                                <li>ZohoCrm EditActions caching data issue fixed</li>
                                <li>Fluent CRM issues fixed</li>
                            </ul>
                        </div>
                    </div>
                    {/* <div>
                        <span style={{ margin: '0px 5px 5px' }}>{__('For more details,')}</span>
                        <a href="https://bitapps.pro/docs/bit-form/changelog/" target="_blank" rel="noreferrer">
                            {__('Click here ')}
                            <ExternalLinkIcn size="14" />
                        </a>
                    </div> */}
                </div>
            </Modal >
        </div >
    )
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
    badge: {
        backgroundColor: 'red',
        color: 'white',
        padding: '4px 8px',
        textAlign: 'center',
        borderRadius: '5px',
    }
}
