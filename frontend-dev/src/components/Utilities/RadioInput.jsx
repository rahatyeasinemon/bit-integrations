import React from 'react';

const RadioInput = ({ name, options, selectedAuthType, handleChange }) => {
  return (
    <div>
      {options.map((option, index) => (
        <label key={index} className='btn btcd-btn-o-purple ml-2'>
          <input
            type="radio"
            name={name}
            value={option}
            checked={selectedAuthType === option}
            onClick={() => handleChange(option)}
          />
          {option}
        </label>
      ))}
    </div>
  );
};

export default RadioInput;
