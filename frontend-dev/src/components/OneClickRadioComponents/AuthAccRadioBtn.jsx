import React, { useState, useEffect, useRef } from 'react';
import bitsFetch from '../../Utils/bitsFetch';
import TrashIcn from '../../Icons/TrashIcn';

const AuthAccRadioBtn = ({ authData, setAuthData, selectedUserIndex, setSelectedUserIndex, isInfo, setIsLoading }) => {
  const [showConfirm, setShowConfirm] = useState(null);
  const popoverRef = useRef(null); // Ref to the popover container

  const handleDelete = (id, index) => {
    setIsLoading(true)
    bitsFetch(id, 'auth/account/delete').then((res) => {
      if (res.success) {
        setAuthData((prevData => prevData.filter(item => item.id !== id)));
        if (selectedUserIndex === index) { //if the selected account is deleted.
          setSelectedUserIndex((authData.length - 2));
        } else if (selectedUserIndex !== index && index <= selectedUserIndex) { //if the deleted account index is less than selected account.
          setSelectedUserIndex(selectedUserIndex - 1)
        }
        setIsLoading(false)
      }
    });

    setShowConfirm(null);
  };

  const handleConnectionSelection = (index) => {
    setSelectedUserIndex(index);
  };

  const handleShowConfirm = (index) => {
    setShowConfirm(index);
  };

  const handleCancel = () => {
    setShowConfirm(null);
  };

  // Listen for clicks outside the popover
  useEffect(() => {
    const handleClickOutside = (event) => {
      if (popoverRef.current && !popoverRef.current.contains(event.target)) {
        setShowConfirm(null);
      }
    };

    document.addEventListener('mousedown', handleClickOutside);

    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, []);

  useEffect(() => {
    if (authData.length === 1 && !isInfo) { //if their is only one authorized account.
      setSelectedUserIndex(0);
    } else if (selectedUserIndex === null && !isInfo) {  //if no account is selected. 
      setSelectedUserIndex(authData.length - 1);
    }
  }, [authData]);

  return (
    <div className='user-radio-input'>
      <div className="auth-list">
        {authData.map((user, index) => (
          <div key={index} className={`auth-item ${selectedUserIndex === index ? 'active' : ''}`}>
            <label className="auth-label">
              <input
                type="radio"
                name="auth"
                value={index}
                checked={selectedUserIndex === index}
                onChange={() => handleConnectionSelection(index)}
                className="radio-input"
              />
              <div className="auth-info">
                <img src={user.userInfo.user.photoLink} alt="User Avatar" className="user-avatar" />
                <div>
                  <div className="user-name">{user.userInfo.user.displayName}</div>
                  <div className="user-email">{user.userInfo.user.emailAddress}</div>
                </div>
              </div>
            </label>
            {!isInfo && (
              <div className="delete-section">
                {showConfirm === index ? (
                  <div className="confirmation-popover" ref={popoverRef}>
                    <p>Are you sure?</p>
                    <button className="confirm-button" onClick={() => handleDelete(user.id, index)}>Yes</button>
                    <button className="cancel-button" onClick={handleCancel}>No</button>
                  </div>
                ) : (
                  <button className="delete-button" onClick={() => handleShowConfirm(index)}>
                    <TrashIcn />
                  </button>
                )}
              </div>
            )}
          </div>
        ))}
      </div>
    </div>
  );
};

export default AuthAccRadioBtn;
