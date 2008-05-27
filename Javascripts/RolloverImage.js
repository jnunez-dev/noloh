function _NTglRlOvrImg(id, state)
{
	var img = _N(id);
	if(img.Cur != state && (img.Selected == null || !img.Selected))
	{
		if(state == 'Slct')
		{
			_NSetProperty(id, 'Selected', true);
			if(img.Select != null)
				img.Select.call();	
		}
		img.src = img[state];
		if(img.onchange != null)
			img.onchange.call();
		img.Cur = state;
	}
	else if(state == 'Slct' && (img.Tgl))
	{
		img.src = img['Out'];
		_NSetProperty(id, 'Selected', false);
		if(img.onchange != null)
			img.onchange.call();
		img.Cur = 'Out';
	}
}