/**
 * Composable para configurar scroll apenas no conteúdo da página
 * Usa a style-fn do Quasar para calcular altura automaticamente
 */
export function usePageScroll() {
  const pageTweak = (offset) => {
    return {
      paddingTop: "0px", // por conta do header fixo
      minHeight: offset ? `calc(100vh - ${offset}px)` : "100vh",
      // height: offset ? `calc(100vh - ${offset}px)` : "100vh",
      overflowY: "auto",
    };
  };

  return {
    pageTweak,
  };
}
