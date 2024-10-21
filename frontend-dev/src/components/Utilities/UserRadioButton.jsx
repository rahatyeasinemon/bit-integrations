import React, { useState, useEffect } from 'react';
import bitsFetch from '../../Utils/bitsFetch';

const UserRadioButton = ({ authData, setAuthData, selectedUser, setSelectedUser, handleAuthUser, isInfo }) => {
  const [showConfirm, setShowConfirm] = useState(null);

  const handleDelete = (id) => {
    const newAuthData = authData.filter(item => item.id !== id);
    bitsFetch(id, 'auth/account/delete').then((res) => {
      if (res.success) {
        setAuthData(newAuthData);
      }
    });
    setShowConfirm(null);
  };

  const handleShowConfirm = (index) => {
    setShowConfirm(index);
  };

  const handleCancel = () => {
    setShowConfirm(null);
  };

  useEffect(() => {
    if (authData.length > 0 && selectedUser === null) {
      setSelectedUser(authData.length - 1);
    }
  }, [authData, selectedUser, setSelectedUser]);

  return (
    <div className='user-radio-input'>
      <div className="auth-list">
        {authData.map((user, index) => (
          <div key={index} className={`auth-item ${selectedUser === index ? 'active' : ''}`}>
            <label className="auth-label">
              <input
                type="radio"
                name="auth"
                value={index}
                checked={selectedUser === index}
                onChange={() => handleAuthUser(index)}
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
                  <div className="confirmation-popover">
                    <p>Are you sure?</p>
                    <button className="confirm-button" onClick={() => handleDelete(user.id)}>Yes</button>
                    <button className="cancel-button" onClick={handleCancel}>No</button>
                  </div>
                ) : (
                  <button className="delete-button" onClick={() => handleShowConfirm(index)}>
                    🗑
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

export default UserRadioButton;
