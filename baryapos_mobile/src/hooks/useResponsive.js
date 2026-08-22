import { useWindowDimensions } from 'react-native';

export const useResponsive = () => {
  const { width, height } = useWindowDimensions();

  const isTablet = width >= 768;
  const isPhone = width < 768;
  const isLandscape = width > height;
  const isPortrait = height >= width;

  let numColumns = 2;
  if (isTablet) {
    numColumns = isLandscape ? 4 : 3;
  } else {
    numColumns = isLandscape ? 3 : 2;
  }

  return {
    width,
    height,
    isTablet,
    isPhone,
    isLandscape,
    isPortrait,
    numColumns,
  };
};

export default useResponsive;
