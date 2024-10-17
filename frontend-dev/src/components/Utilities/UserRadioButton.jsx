import React, { useEffect } from 'react';
import bitsFetch from '../../Utils/bitsFetch';

const UserRadioButton = ({ authData,setAuthData, selectedUser, handleAuthUser, isInfo }) => {

    const handleDelete = (id) => {
        const newAuthData = authData.filter(item => item.id !== id);
        setAuthData(newAuthData);
        bitsFetch(id, 'auth/account/delete')
    }
    
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
                {!isInfo && <button className="delete-button" onClick={() => handleDelete(user.id)}>
                    🗑
                </button>}
            </div>
        ))}
        </div>
    </div>
  );
};

export default UserRadioButton;
