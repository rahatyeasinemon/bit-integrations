import { useState } from 'react'
import announcement from '../resource/img/announcement.svg'
import promo from '../resource/img/early_bird_offer.webp'
import ExternalLinkIcn from '../Icons/ExternalLinkIcn'
import { __, sprintf } from '../Utils/i18nwrap'
import '../resource/css/cashback-modal.css'
import Modal from '../components/Utilities/Modal'
import '../resource/css/newRelease.css'

const PRODUCT_NAME = 'Bit Integrations'
const REVIEW_URL = 'https://bit-social.com/'

export default function AnnouncementModal() {
  const [show, setShow] = useState(false)

  // if (!btcbi.isPro) return

  return (
    <div className="announcement-modal">
      <button
        title={__('New Product Realease', 'bit-integrations')}
        type="button"
        className="announcement-btn"
        onClick={() => setShow(true)}>
        <img src={announcement} style={{ marginRight: '8px', width: '30px' }} />
        {__('New Product Realease', 'bit-integrations')}
        <span className="star" />
        <span className="star" />
        <span className="star" />
      </button>
      <Modal md show={show} style={{ width: '33%',height:'85%'}} setModal={() => setShow(false)} noPadding={true} closeIcon={true}>
        <div>
          <a href={REVIEW_URL} target="_blank" rel="noreferrer">
          <img src={promo} style={{width: '100%', marginTop:'-10px'}} alt="" />
          </a>
        </div>
        <div className="footer-wrapper" style={{ position: 'absolute', zIndex: '1', bottom:'-25px', left: '50%', transform:'translateX(-50%)' }} >
          <a className="footer-btn purple" href={REVIEW_URL} target="_blank" rel="noreferrer">
            {__('Grab the Deal', 'bit-integrations')}
            <ExternalLinkIcn size={16} className="" />
          </a>
        </div>
      </Modal>
    </div>
  )
}
